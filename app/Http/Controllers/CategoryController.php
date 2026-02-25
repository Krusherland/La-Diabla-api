<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Get all categories (public)
     */
    public function index()
    {
        $categories = Category::where('is_active', true)
            ->withCount('activeProducts')
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'categories' => $categories
        ], 200);
    }

    /**
     * Get all categories including inactive (admin)
     */
    public function adminIndex()
    {
        $categories = Category::withCount('products')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'categories' => $categories
        ], 200);
    }

    /**
     * Get single category with products
     */
    public function show($id)
    {
        $category = Category::with(['activeProducts' => function($query) {
            $query->orderBy('name', 'asc');
        }])->find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'category' => $category
        ], 200);
    }

    /**
     * Create new category (admin)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:categories',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 400);
        }

        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Category created successfully',
            'category' => $category
        ], 201);
    }

    /**
     * Update category (admin)
     */
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:100|unique:categories,name,' . $id,
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 400);
        }

        $updateData = [];
        
        if ($request->has('name')) {
            $updateData['name'] = $request->name;
        }
        if ($request->has('description')) {
            $updateData['description'] = $request->description;
        }
        if ($request->has('is_active')) {
            $updateData['is_active'] = $request->is_active;
        }

        $category->update($updateData);

        return response()->json([
            'status' => 'success',
            'message' => 'Category updated successfully',
            'category' => $category
        ], 200);
    }

    /**
     * Delete category (admin)
     */
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found'
            ], 404);
        }

        // Check if category has products
        if ($category->products()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete category with existing products'
            ], 400);
        }

        $category->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Category deleted successfully'
        ], 200);
    }
}
