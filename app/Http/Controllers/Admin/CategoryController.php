<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::whereNull('parent_id')->with(['children', 'products']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('children', function($sub) use ($search) {
                      $sub->where('name', 'like', "%{$search}%")
                          ->orWhere('code', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('main_group') && $request->main_group !== 'all') {
            $query->where('id', $request->main_group);
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        $categories = $query->orderBy('name')->paginate(8)->withQueryString();
        $mainCategories = Category::whereNull('parent_id')->orderBy('name')->get();

        return view('admin.categories.categoryManagement', [
            'categories' => $categories,
            'mainCategories' => $mainCategories
        ]);
    }

    private function generateUniqueCode($name)
    {
        // Generate a clean slug/acronym from name (e.g. "Water Filters" -> "WATER-FILTERS" or "WF")
        $baseCode = strtoupper(Str::slug($name, ''));
        if (strlen($baseCode) > 6) {
            $baseCode = substr($baseCode, 0, 6);
        }
        if (empty($baseCode)) {
            $baseCode = 'CAT';
        }

        $code = $baseCode;
        $counter = 1;

        while (Category::where('code', $code)->exists()) {
            $code = $baseCode . $counter;
            $counter++;
        }

        return $code;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'type' => ['required', 'in:product,service'],
            'description' => ['nullable', 'string'],
        ]);

        mw:
        $parentId = $data['parent_id'] ?? null;

        $exists = Category::where('parent_id', $parentId)
            ->whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($data['name']))])
            ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'A category with this name already exists under this group.'])->withInput();
        }

        if ($parentId) {
            $parentCategory = Category::find($parentId);
            if ($parentCategory) {
                $data['type'] = $parentCategory->type;
            }
        }

        $data['code'] = $this->generateUniqueCode($data['name']);
        $data['status'] = 'ACTIVE';
        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function storeSubcategory(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $exists = Category::where('parent_id', $category->id)
            ->whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($data['name']))])
            ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'This brand subcategory already exists under this category.'])->withInput();
        }

        $category->children()->create([
            'name' => $data['name'],
            'code' => $this->generateUniqueCode($data['name']),
            'type' => $category->type,
            'status' => 'ACTIVE',
            'description' => $data['description'] ?? null,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Brand subcategory added successfully.');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:product,service'],
            'description' => ['nullable', 'string'],
        ]);

        // Keep existing code or regenerate if name changed significantly
        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        if ($category->children()->exists()) {
            return back()->with('error', 'Cannot delete a category that still has subcategories assigned.');
        }

        if ($category->products()->exists()) {
            return back()->with('error', 'Cannot delete a category that still has products assigned.');
        }

        if ($category->attachment_path) {
            Storage::disk('public')->delete($category->attachment_path);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }
}