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
                    $q->where(function ($subQuery) use ($term) {
                        $subQuery->where('product_name', 'like', '%' . $term . '%')
                                 ->orWhere('code', 'like', '%' . $term . '%')
                                 ->orWhereHas('category', function ($catQuery) use ($term) {
                                     $catQuery->where('name', 'like', '%' . $term . '%');
                                 });
                    });
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
        $allowedSorts = ['product_name', 'code', 'store_quantity', 'warehouse_quantity', 'selling_price', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'product_name';
        }

        $products = $query->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc')
            ->paginate(16)
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
                'delivery_status' => $requiresApproval ? 'pending' : 'received',
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

        if ($data['direction'] === 'to_store' && $product->warehouse_quantity < $data['quantity']) {
            return back()->with('error', 'Not enough warehouse stock to transfer. Available warehouse quantity: ' . $product->warehouse_quantity);
        }

        if ($data['direction'] === 'to_warehouse' && $product->store_quantity < $data['quantity']) {
            return back()->with('error', 'Not enough store stock to transfer. Available store quantity: ' . $product->store_quantity);
        }

        DB::transaction(function () use ($product, $data, $user) {
            if ($data['direction'] === 'to_store') {
                $product->decrement('warehouse_quantity', $data['quantity']);
                $product->increment('store_quantity', $data['quantity']);
                $type = 'transfer_to_store';
            } else {
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
                'delivery_status' => 'received',
                'quantity' => $data['quantity'],
                'note' => $data['note'] ?? null,
                'approved_at' => now(),
            ]);
        });

        return back()->with('success', 'Stock transferred successfully.');
    }

    public function pendingApprovals()
    {
        $pending = StockMovement::with(['product', 'requester'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->paginate(4);

        return view('inventory.pendingApprovals', compact('pending'));
    }

    public function approve(StockMovement $movement)
    {
        if ($movement->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        $movement->update([
            'status' => 'approved',
            'delivery_status' => 'in_transit',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Restock approved and is now In Transit.');
    }

    public function fulfill(StockMovement $movement)
    {
        if ($movement->delivery_status !== 'in_transit' && $movement->status !== 'approved') {
            return back()->with('error', 'Only in-transit or approved requests can be received.');
        }

        DB::transaction(function () use ($movement) {
            $product = $movement->product;
            $column = str_contains($movement->type, 'warehouse') ? 'warehouse_quantity' : 'store_quantity';

            $before = $product->$column;
            $product->increment($column, $movement->quantity);
            $after = $before + $movement->quantity;

            $movement->delivery_status = 'received';
            $movement->quantity_before = $before;
            $movement->quantity_after = $after;
            $movement->save();
        });

        return back()->with('success', 'Restock marked as received and stock updated successfully.');
    }

    public function reject(Request $request, StockMovement $movement)
    {
        if ($movement->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $movement->status = 'rejected';
        $movement->delivery_status = 'not_approved';
        $movement->approved_by = Auth::id();
        $movement->approved_at = now();
        $movement->rejection_reason = $data['rejection_reason'];
        $movement->save();

        return back()->with('success', 'Restock request rejected.');
    }

    public function logs(Request $request)
    {
        $query = StockMovement::with(['product.category', 'requester', 'approver'])
            ->where('status', '!=', 'pending'); // Synchronized to exclude pending requests from logs[cite: 15]

        if ($request->filled('category_id')) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        $movements = $query->orderBy('created_at', 'desc')->get();
        $grouped = $movements->groupBy(fn($m) => $m->batch_id ?? 'single-' . $m->id);

        $page = $request->get('page', 1);
        $perPage = 4;
        $paginatedGroups = new \Illuminate\Pagination\LengthAwarePaginator(
            $grouped->forPage($page, $perPage)->values(),
            $grouped->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $categories = Category::orderBy('name')->get();
        $grouped = $paginatedGroups;

        return view('inventory.stockLogs', compact('grouped', 'categories'));
    }

    public function myRequests(Request $request)
    {
        $query = StockMovement::with(['product', 'approver'])
            ->where('requested_by', Auth::id());

        if ($request->filled('status')) {
            $query->where('delivery_status', $request->status);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('inventory.myRequests', compact('requests'));
    }

    public function storeRequest(Request $request)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['staff', 'warehouse_staff'])) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'location' => ['required', 'in:store,warehouse'],
            'quantity' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $product = Product::findOrFail($data['product_id']);
        $column = $data['location'] === 'store' ? 'store_quantity' : 'warehouse_quantity';
        $type = $data['location'] === 'store' ? 'restock_store' : 'restock_warehouse';

        DB::transaction(function () use ($product, $data, $user, $column, $type) {
            StockMovement::create([
                'product_id' => $product->id,
                'requested_by' => $user->id,
                'approved_by' => null,
                'type' => $type,
                'status' => 'pending',
                'delivery_status' => 'pending',
                'quantity_before' => $product->$column,
                'quantity_after' => $product->$column,
                'quantity' => $data['quantity'],
                'note' => $data['note'] ?? null,
            ]);
        });

        return back()->with('success', 'Restock request submitted successfully for approval.');
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
            'code' => $product->code ?? '—',
            'approx_volume' => $product->approx_volume ?: '—',
            'purchase_cost' => $product->purchase_cost !== null ? number_format($product->purchase_cost, 2) : '—',
            'selling_price' => $product->selling_price !== null ? number_format($product->selling_price, 2) : '—',
            'store_quantity' => $product->store_quantity ?? 0,
            'warehouse_quantity' => $product->warehouse_quantity ?? 0,
            'image' => $product->product_image ? asset('storage/' . $product->product_image) : null,
        ]);
    }
}