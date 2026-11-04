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

        if ($request->filled('search')) {
            $searchTerm = trim($request->search);
            $query->where(function ($q) use ($searchTerm) {
                $q->where('product_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('product_sku', 'like', '%' . $searchTerm . '%')
                  ->orWhere('item_sku', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $sortField = $request->input('sort', 'product_name');
        $sortDirection = $request->input('direction', 'asc');
        
        $allowedSorts = ['product_name', 'product_sku', 'item_sku', 'store_quantity', 'warehouse_quantity', 'selling_price', 'purchase_cost', 'created_at'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection === 'desc' ? 'desc' : 'asc');
        }

        $products = $query->paginate(12)->withQueryString();
        // Fetch parent categories with their children for structured hierarchical dropdowns
        $categories = Category::whereNull('parent_id')->with('children')->where('type', 'product')->orderBy('name')->get();

        $autoProductSku = 'PRD-' . strtoupper(substr(uniqid(), -6));
        $autoItemSku = $autoProductSku . '-ITM-01';

        return view('admin.products.productManagement', compact('products', 'categories', 'autoProductSku', 'autoItemSku'));
    }

    public function create()
    {
        $categories = Category::whereNull('parent_id')->with('children')->where('type', 'product')->orderBy('name')->get();
        $autoProductSku = 'PRD-' . strtoupper(substr(uniqid(), -6));
        $autoItemSku = $autoProductSku . '-ITM-01';

        return view('admin.products.addProduct', compact('categories', 'autoProductSku', 'autoItemSku'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'product_description' => ['nullable', 'string'],
            'approx_volume' => ['nullable', 'string', 'max:255'],
            'purchase_cost' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'store_quantity' => ['required', 'integer', 'min:0'],
            'warehouse_quantity' => ['required', 'integer', 'min:0'],
            'reorder_point' => ['nullable', 'integer', 'min:0'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'product_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $prefix = 'PRD';
        if ($request->filled('category_id')) {
            $category = Category::find($request->category_id);
            if ($category) {
                $cleaned = strtoupper(preg_replace('/[^a-zA-Z]/', '', $category->name));
                if (strlen($cleaned) >= 3) {
                    $prefix = substr($cleaned, 0, 3);
                } elseif (strlen($cleaned) > 0) {
                    $prefix = $cleaned;
                }
            }
        }

        $productSku = $prefix . '-' . strtoupper(substr(uniqid(), -6));
        $itemSku = $productSku . '-ITM-01';

        $product = new Product();
        $product->product_sku = $productSku;
        $product->item_sku = $itemSku;
        $product->product_name = $request->product_name;
        $product->product_description = $request->product_description ?? '';
        $product->approx_volume = $request->approx_volume ?? '';
        $product->purchase_cost = $request->purchase_cost;
        $product->selling_price = $request->selling_price;
        $product->store_quantity = $request->store_quantity;
        $product->warehouse_quantity = $request->warehouse_quantity;
        $product->reorder_point = $request->reorder_point ?? 0;
        $product->category_id = $request->category_id;

        if ($request->hasFile('product_image')) {
            $product->product_image = $request->file('product_image')->store('products', 'public');
        }

        $product->save();

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::whereNull('parent_id')->with('children')->where('type', 'product')->orderBy('name')->get();
        return view('admin.products.editProduct', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'product_sku' => ['required', 'string', 'max:255', 'unique:products,product_sku,' . $product->id],
            'item_sku' => ['nullable', 'string', 'max:255', 'unique:products,item_sku,' . $product->id],
            'product_name' => ['required', 'string', 'max:255'],
            'product_description' => ['nullable', 'string'],
            'approx_volume' => ['nullable', 'string', 'max:255'],
            'purchase_cost' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'store_quantity' => ['required', 'integer', 'min:0'],
            'warehouse_quantity' => ['required', 'integer', 'min:0'],
            'reorder_point' => ['nullable', 'integer', 'min:0'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'product_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        if ($request->hasFile('product_image')) {
            if ($product->product_image && Storage::disk('public')->exists($product->product_image)) {
                Storage::disk('public')->delete($product->product_image);
            }
            $product->product_image = $request->file('product_image')->store('products', 'public');
        }

        $product->product_sku = $request->product_sku;
        $product->item_sku = $request->item_sku;
        $product->product_name = $request->product_name;
        $product->product_description = $request->product_description ?? '';
        $product->approx_volume = $request->approx_volume ?? '';
        $product->purchase_cost = $request->purchase_cost;
        $product->selling_price = $request->selling_price;
        $product->store_quantity = $request->store_quantity;
        $product->warehouse_quantity = $request->warehouse_quantity;
        $product->reorder_point = $request->reorder_point ?? 0;
        $product->category_id = $request->category_id;

        $product->save();

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($product->product_image && Storage::disk('public')->exists($product->product_image)) {
            Storage::disk('public')->delete($product->product_image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}