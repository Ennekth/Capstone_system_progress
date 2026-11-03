<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
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

        $sort = $request->get('sort', 'product_name');
        $direction = $request->get('direction', 'asc');

        $allowedSorts = ['product_name', 'store_quantity', 'warehouse_quantity', 'selling_price', 'purchase_cost', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'product_name';
        }

        $products = $query->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc')
            ->paginate(20)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('admin.products.productManagement', compact('products', 'categories', 'sort', 'direction'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.addProduct', ['categories' => $categories]);
    }

    private function generateUniqueSku(string $productName): string
    {
        $slug = strtoupper(Str::slug($productName, ''));
        $prefix = strlen($slug) >= 3 ? substr($slug, 0, 3) : 'PRD';

        do {
            // Generates a unique SKU like PUR-K92X1, PUR-M47L2, etc.
            $sku = $prefix . '-' . strtoupper(Str::random(5));
        } while (Product::where('code', $sku)->exists());

        return $sku;
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
            'product_image'       => ['nullable', 'image', 'max:2048'],
        ]);

        // Automatically assign a guaranteed unique one-of-one SKU code
        $data['code'] = $this->generateUniqueSku($data['product_name']);

        $data['category_id'] = $data['category_id'] ?? Category::getOrCreateDefault()->id;
        $data['product_name'] = strip_tags($data['product_name']);
        $data['product_description'] = strip_tags($data['product_description'] ?? '');
        $data['store_quantity'] = $data['store_quantity'] ?? 0;
        $data['warehouse_quantity'] = $data['warehouse_quantity'] ?? 0;

        if ($request->hasFile('product_image')) {
            $data['product_image'] = $request->file('product_image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Product added successfully with a unique SKU.');
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
            'store_quantity'      => ['nullable', 'integer', 'min:0'],
            'warehouse_quantity'  => ['nullable', 'integer', 'min:0'],
            'reorder_point'       => ['nullable', 'integer', 'min:0'],
            'category_id'         => ['nullable', 'exists:categories,id'],
            'approx_volume'       => ['nullable', 'string', 'max:255'],
            'product_image'       => ['nullable', 'image', 'max:2048'],
        ]);

        // Case-insensitive duplicate check excluding current product
        $exists = Product::where('id', '!=', $product->id)
            ->whereRaw('LOWER(TRIM(product_name)) = ?', [strtolower(trim($request->product_name))])
            ->exists();

        if ($exists) {
            return back()->withErrors(['product_name' => 'Another product with this name already exists.'])->withInput();
        }

        $data['product_name'] = strip_tags($data['product_name']);
        $data['product_description'] = strip_tags($data['product_description'] ?? '');

        if ($request->hasFile('product_image')) {
            if ($product->product_image) {
                Storage::disk('public')->delete($product->product_image);
            }
            $data['product_image'] = $request->file('product_image')->store('products', 'public');
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