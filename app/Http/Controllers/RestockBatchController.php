<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RestockBatchController extends Controller
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

        $products = $query->orderBy('product_name')->get();
        $categories = Category::orderBy('name')->get();

        $user = Auth::user();
        $location = $user->role === 'warehouse_staff' ? 'warehouse' : 'store';

        return view('inventory.restockBatch', compact('products', 'categories', 'location'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'location' => ['required', 'in:store,warehouse'],
            'quantities' => ['required', 'array'],
            'quantities.*' => ['nullable', 'integer', 'min:0'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        if ($user->role === 'warehouse_staff' && $data['location'] !== 'warehouse') {
            abort(403, 'Warehouse staff can only manage warehouse stock.');
        }

        if ($user->role === 'staff' && $data['location'] !== 'store') {
            abort(403, 'Staff can only request store restocks.');
        }

        $entries = array_filter($data['quantities'], fn($qty) => $qty !== null && $qty > 0);

        if (empty($entries)) {
            return back()->with('error', 'Enter at least one quantity to restock.');
        }

        $requiresApproval = $user->role === 'staff';
        $batchId = Str::uuid();
        $type = $data['location'] === 'store' ? 'restock_store' : 'restock_warehouse';
        $column = $data['location'] === 'store' ? 'store_quantity' : 'warehouse_quantity';

        DB::transaction(function () use ($entries, $batchId, $type, $column, $user, $requiresApproval, $data) {
            foreach ($entries as $productId => $qty) {
                $product = Product::find($productId);
                if (!$product) {
                    continue;
                }

                $before = $product->$column;
                $after = $requiresApproval ? $before : $before + $qty;

                StockMovement::create([
                    'batch_id' => $batchId,
                    'product_id' => $product->id,
                    'requested_by' => $user->id,
                    'approved_by' => $requiresApproval ? null : $user->id,
                    'type' => $type,
                    'status' => $requiresApproval ? 'pending' : 'approved',
                    'quantity_before' => $before,
                    'quantity_after' => $after,
                    'quantity' => $qty,
                    'note' => $data['note'] ?? null,
                    'approved_at' => $requiresApproval ? null : now(),
                    'created_at' => now(), // Explicitly captures current date and time
                    'updated_at' => now(), // Explicitly captures current date and time
                ]);

                if (!$requiresApproval) {
                    $product->increment($column, $qty);
                }
            }
        });

        $count = count($entries);
        $message = $requiresApproval
            ? "Restock batch submitted for approval ({$count} item(s))."
            : "Restock batch applied successfully ({$count} item(s)).";

        return redirect()->route('inventory.index')->with('success', $message);
    }
}