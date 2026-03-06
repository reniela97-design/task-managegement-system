<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = Category::where('category_inactive', false);

        if ($request->filled('search')) {
            $query->where('category_name', 'like', "%{$request->search}%");
        }

        $categories = $query->get();
        return view('categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255',
        ]);

        Category::create([
            'category_name' => $validated['category_name'],
            'category_inactive' => false,
        ]);

        return redirect()->route('categories.index')->with('status', 'Category created successfully!');
    }

    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('status', 'Category updated successfully!');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->update(['category_inactive' => true]);
        return redirect()->route('categories.index')->with('status', 'Category deleted successfully!');
    }
}