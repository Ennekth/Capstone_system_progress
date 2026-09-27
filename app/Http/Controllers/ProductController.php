<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->orderBy('product_name')->get();
        return view('admin.products.productManagement', ['products' => $products]);
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.addProduct', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_name'        => ['required', 'string', 'min:3', 'max:255'],
            'product_description' => ['nullable', 'string'],
            'purchase_cost'       => ['required', 'numeric', 'min:0'],
            'selling_price'       => ['required', 'numeric', 'min:0'],
            'quantity'            => ['nullable', 'integer', 'min:0'],
            'reorder_point'       => ['nullable', 'integer', 'min:0'],
            'category_id'         => ['required', 'exists:categories,id'],
            'approx_volume'       => ['nullable', 'string', 'max:255'],
            'product_image'       => ['nullable', 'image', 'max:2048'],
        ]);

        $exists = Product::whereRaw('LOWER(TRIM(product_name)) = ?', [strtolower(trim($request->product_name))])->exists();

        if ($exists) {
            return back()->withErrors(['product_name' => 'A product with this name already exists.'])->withInput();
        }

        $data['product_name'] = strip_tags($data['product_name']);
        $data['product_description'] = strip_tags($data['product_description'] ?? '');
        $data['quantity'] = $data['quantity'] ?? 0;

        if ($request->hasFile('product_image')) {
            $data['product_image'] = $request->file('product_image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Product added successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.editProduct', ['product' => $product, 'categories' => $categories]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'product_name'        => ['required', 'string', 'min:3', 'max:255'],
            'product_description' => ['nullable', 'string'],
            'purchase_cost'       => ['required', 'numeric', 'min:0'],
            'selling_price'       => ['required', 'numeric', 'min:0'],
            'quantity'            => ['required', 'integer', 'min:0'],
            'reorder_point'       => ['nullable', 'integer', 'min:0'],
            'category_id'         => ['required', 'exists:categories,id'],
            'approx_volume'       => ['nullable', 'string', 'max:255'],
            'product_image'       => ['nullable', 'image', 'max:2048'],
        ]);

        $data['product_name'] = strip_tags($data['product_name']);
        $data['product_description'] = strip_tags($data['product_description'] ?? '');

        if ($request->hasFile('product_image')) {
            if ($product->product_image) {
                Storage::disk('public')->delete($product->product_image);
            }
            $data['product_image'] = $request->file('product_image')->store('products', 'public');
        }

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