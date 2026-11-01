<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $searchTerms = array_filter(explode(' ', trim($request->search)));
            $query->where(function ($q) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $q->where('product_name', 'like', '%' . $term . '%');
                }
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'low') {
                $query->whereColumn('store_quantity', '<=', 'reorder_point')->whereNotNull('reorder_point');
            } elseif ($request->status === 'out') {
                $query->where('store_quantity', '<=', 0);
            } elseif ($request->status === 'restock') {
                $query->where(function ($q) {
                    $q->where('store_quantity', '<=', 0)
                      ->orWhere(function ($sub) {
                          $sub->whereColumn('store_quantity', '<=', 'reorder_point')
                              ->whereNotNull('reorder_point');
                      });
                });
            }
        }

        $sort = $request->get('sort', 'product_name');
        $direction = $request->get('direction', 'asc');
        $allowedSorts = ['product_name', 'store_quantity', 'warehouse_quantity', 'selling_price', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'product_name';
        }

        $products = $query->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc')
            ->paginate(20)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('inventory.inventoryManagement', compact('products', 'categories', 'sort', 'direction'));
    }

    public function restock(Request $request, Product $product)
    {
        $user = Auth::user();

        $data = $request->validate([
            'location' => ['required', 'in:store,warehouse'],
            'quantity' => ['required', 'integer', 'min:1'],
            'type' => ['required', 'in:restock_store,restock_warehouse,assembly,adjustment'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        if ($user->role === 'warehouse_staff' && $data['location'] !== 'warehouse') {
            abort(403, 'Warehouse staff can only manage warehouse stock.');
        }

        if ($user->role === 'staff' && $data['location'] !== 'store') {
            abort(403, 'Staff can only request store restocks.');
        }

        // Keep the log label consistent with the chosen location
        if (in_array($data['type'], ['restock_store', 'restock_warehouse'])) {
            $data['type'] = $data['location'] === 'store' ? 'restock_store' : 'restock_warehouse';
        }

        $requiresApproval = $user->role === 'staff';
        $column = $data['location'] === 'store' ? 'store_quantity' : 'warehouse_quantity';

        DB::transaction(function () use ($product, $data, $user, $requiresApproval, $column) {
            $before = $product->$column;
            $after = $requiresApproval ? $before : $before + $data['quantity'];

            StockMovement::create([
                'product_id' => $product->id,
                'requested_by' => $user->id,
                'approved_by' => $requiresApproval ? null : $user->id,
                'type' => $data['type'],
                'status' => $requiresApproval ? 'pending' : 'approved',
                'quantity_before' => $before,
                'quantity_after' => $after,
                'quantity' => $data['quantity'],
                'note' => $data['note'] ?? null,
                'approved_at' => $requiresApproval ? null : now(),
            ]);

            if (!$requiresApproval) {
                $product->increment($column, $data['quantity']);
            }
        });

        $message = $requiresApproval
            ? 'Restock request submitted for approval.'
            : 'Stock updated successfully.';

        return back()->with('success', $message);
    }

    public function transfer(Request $request, Product $product)
    {
        $user = Auth::user();

        if ($user->role === 'warehouse_staff' || $user->role === 'staff') {
            abort(403, 'You are not authorized to transfer stock between locations.');
        }

        $data = $request->validate([
            'direction' => ['required', 'in:to_store,to_warehouse'],
            'quantity' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($product, $data, $user) {
            if ($data['direction'] === 'to_store') {
                if ($product->warehouse_quantity < $data['quantity']) {
                    abort(422, 'Not enough warehouse stock to transfer.');
                }
                $product->decrement('warehouse_quantity', $data['quantity']);
                $product->increment('store_quantity', $data['quantity']);
                $type = 'transfer_to_store';
            } else {
                if ($product->store_quantity < $data['quantity']) {
                    abort(422, 'Not enough store stock to transfer.');
                }
                $product->decrement('store_quantity', $data['quantity']);
                $product->increment('warehouse_quantity', $data['quantity']);
                $type = 'transfer_to_warehouse';
            }

            StockMovement::create([
                'product_id' => $product->id,
                'requested_by' => $user->id,
                'approved_by' => $user->id,
                'type' => $type,
                'status' => 'approved',
                'quantity' => $data['quantity'],
                'note' => $data['note'] ?? null,
                'approved_at' => now(),
            ]);
        });

        return back()->with('success', 'Stock transferred successfully.');
    }

    // ==================== APPROVALS (admin/manager only) ====================

    public function pendingApprovals()
    {
        $pending = StockMovement::with(['product', 'requester'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        return view('inventory.pendingApprovals', compact('pending'));
    }

    public function approve(StockMovement $movement)
    {
        if ($movement->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        DB::transaction(function () use ($movement) {
            $product = $movement->product;
            $column = str_contains($movement->type, 'warehouse') ? 'warehouse_quantity' : 'store_quantity';

            $before = $product->$column;
            $product->increment($column, $movement->quantity);
            $after = $before + $movement->quantity;

            $movement->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'quantity_before' => $before,
                'quantity_after' => $after,
            ]);
        });

        return back()->with('success', 'Restock approved and applied.');
    }

    public function reject(Request $request, StockMovement $movement)
    {
        if ($movement->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $movement->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => $data['rejection_reason'],
        ]);

        return back()->with('success', 'Restock request rejected.');
    }

    // ==================== LOGS ====================

    public function logs(Request $request)
    {
        $query = StockMovement::with(['product', 'requester', 'approver'])
            ->where('status', '!=', 'pending');

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $movements = $query->orderBy('created_at', 'desc')->get();

        // Group by batch_id (ungrouped/null batch entries each become their own group)
        $grouped = $movements->groupBy(fn($m) => $m->batch_id ?? 'single-' . $m->id);

        $products = Product::orderBy('product_name')->get();

        return view('inventory.stockLogs', compact('grouped', 'products'));
    }

    // ==================== Staff REQUESTS ====================
    public function myRequests(Request $request)
    {
        $query = StockMovement::with(['product', 'approver'])
            ->where('requested_by', Auth::id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('inventory.myRequests', compact('requests'));
    }

    public function cancelRequest(StockMovement $movement)
    {
        if ($movement->requested_by !== Auth::id()) {
            abort(403, 'You can only cancel your own requests.');
        }

        if ($movement->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be cancelled.');
        }

        $movement->delete();

        return back()->with('success', 'Request withdrawn successfully.');
    }

    public function editRequest(StockMovement $movement)
    {
        if ($movement->requested_by !== Auth::id()) {
            abort(403, 'You can only edit your own requests.');
        }

        if ($movement->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be edited.');
        }

        return view('inventory.editRequest', compact('movement'));
    }

    public function updateRequest(Request $request, StockMovement $movement)
    {
        if ($movement->requested_by !== Auth::id()) {
            abort(403, 'You can only edit your own requests.');
        }

        if ($movement->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be edited.');
        }

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $movement->update([
            'quantity' => $data['quantity'],
            'note' => $data['note'] ?? null,
        ]);

        return redirect()->route('inventory.myRequests')->with('success', 'Request updated successfully.');
    }

    public function show(Product $product)
    {
        return response()->json([
            'id' => $product->id,
            'name' => $product->product_name,
            'description' => $product->product_description ?: 'No description provided.',
            'category' => $product->category->name ?? '—',
            'approx_volume' => $product->approx_volume ?: '—',
            'purchase_cost' => $product->purchase_cost !== null ? number_format($product->purchase_cost, 2) : '—',
            'selling_price' => $product->selling_price !== null ? number_format($product->selling_price, 2) : '—',
            'store_quantity' => $product->store_quantity ?? 0,
            'warehouse_quantity' => $product->warehouse_quantity ?? 0,
            'image' => $product->product_image ? asset('storage/' . $product->product_image) : null,
        ]);
    }
}