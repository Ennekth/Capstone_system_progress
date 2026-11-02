<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('product_description', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Sorting
        $sort = $request->get('sort', 'product_name');
        $direction = $request->get('direction', 'asc');
        
        $allowedSorts = ['product_name', 'store_quantity', 'warehouse_quantity', 'selling_price', 'purchase_cost', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');
        }

        $products = $query->paginate(15)->withQueryString();
        $categories = Category::all();

        return view('admin.products.productManagement', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_name'        => ['required', 'string', 'min:3', 'max:255'],
            'product_description' => ['nullable', 'string'],
            'purchase_cost'       => ['required', 'numeric', 'min:0'],
            'selling_price'       => ['required', 'numeric', 'min:0'],
            'store_quantity'      => ['nullable', 'integer', 'min:0'],
            'warehouse_quantity'  => ['nullable', 'integer', 'min:0'],
            'reorder_point'       => ['nullable', 'integer', 'min:0'],
            'category_id'         => ['nullable', 'exists:categories,id'],
            'approx_volume'       => ['nullable', 'string', 'max:255'],
            'volume_liters'       => ['nullable', 'numeric', 'min:0'],
            'product_image'       => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $exists = Product::whereRaw('LOWER(TRIM(product_name)) = ?', [strtolower(trim($request->product_name))])->exists();

        if ($exists) {
            return back()->withErrors(['product_name' => 'A product with this name already exists.'])->withInput();
        }

        $data['category_id'] = $data['category_id'] ?? Category::getOrCreateDefault()->id;
        $data['product_name'] = strip_tags($data['product_name']);
        $data['product_description'] = strip_tags($data['product_description'] ?? '');
        $data['store_quantity'] = $data['store_quantity'] ?? 0;
        $data['warehouse_quantity'] = $data['warehouse_quantity'] ?? 0;

        if ($request->hasFile('product_image')) {
            $data['product_image'] = $request->file('product_image')->store('products', 'public');
        } else {
            unset($data['product_image']);
        }

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Product added successfully.');
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'product_name'        => ['required', 'string', 'min:3', 'max:255'],
            'product_description' => ['nullable', 'string'],
            'purchase_cost'       => ['required', 'numeric', 'min:0'],
            'selling_price'       => ['required', 'numeric', 'min:0'],
            'store_quantity'      => ['nullable', 'integer', 'min:0'],
            'warehouse_quantity'  => ['nullable', 'integer', 'min:0'],
            'reorder_point'       => ['nullable', 'integer', 'min:0'],
            'category_id'         => ['nullable', 'exists:categories,id'],
            'approx_volume'       => ['nullable', 'string', 'max:255'],
            'volume_liters'       => ['nullable', 'numeric', 'min:0'],
            'product_image'       => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $data['product_name'] = strip_tags($data['product_name']);
        $data['product_description'] = strip_tags($data['product_description'] ?? '');

        if ($request->hasFile('product_image')) {
            if ($product->product_image) {
                Storage::disk('public')->delete($product->product_image);
            }
            $data['product_image'] = $request->file('product_image')->store('products', 'public');
        } else {
            unset($data['product_image']);
        }

        $data['category_id'] = $data['category_id'] ?? Category::getOrCreateDefault()->id;
        
        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($product->product_image) {
            Storage::disk('public')->delete($product->product_image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}