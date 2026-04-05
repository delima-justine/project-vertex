<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // List all categories
    public function index() {
        return response()->json(Category::all());
    }

    // Store a new category
    public function store(Request $request) 
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:50|unique:tbl_category,category_name',
        ]);

        $category = Category::create($validated);
        return response()->json($category, 201);
    }

    // Show a specific category
    public function show(Category $category) 
    {
        return response()->json($category);
    }

    // Update a category
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:50|unique:tbl_category,category_name,' . $category->id,
        ]);

        $category->update($validated);
        return response()->json($category);
    }

    // Delete a category
    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }
}
