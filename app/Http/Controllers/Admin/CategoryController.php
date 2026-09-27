<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.categories.categoryManagement', ['categories' => $categories]);
    }

    public function store(Request $request)
{
    $data = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'type' => ['required', 'in:product,service'],
    ]);

   $exists = Category::whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($data['name']))])
    ->where('type', $data['type'])
    ->exists();

    if ($exists) {
        return back()->withErrors(['name' => 'A category with this name already exists.'])->withInput();
    }

    Category::create($data);

    return redirect()->route('admin.categories.index')->with('success', 'Category added.');
}

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Cannot delete a category that still has products assigned.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted.');
    }
}