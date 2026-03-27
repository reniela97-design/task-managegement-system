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
        $request->validate([
            'category_name' => 'required|string|max:255',
        ]);

        // Check for duplicate category name (C1.3 - Category already in list)
        $existingCategory = Category::where('category_name', $request->category_name)
            ->where('category_inactive', false)
            ->first();
        
        if ($existingCategory) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'The Category is already in the list');
        }

        Category::create([
            'category_name' => $request->category_name,
            'category_inactive' => false,
        ]);

        // C1.4 - Redirect to Category List with success message
        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully!');
    }

    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
        ]);

        // Check for duplicate category name (excluding current category)
        $existingCategory = Category::where('category_name', $request->category_name)
            ->where('category_inactive', false)
            ->where('category_id', '!=', $category->category_id)
            ->first();
        
        if ($existingCategory) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'The Category is already in the list');
        }

        $category->update([
            'category_name' => $request->category_name
        ]);

        // U2.3 - Success message "UPDATE SUCCESSFULLY"
        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->update(['category_inactive' => true]);
        
        // U3.2 - Success message after deletion
        return redirect()->route('categories.index')
            ->with('success', 'Category successfully deleted');
    }
}