<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Get all active products (public)
     */
    public function index(Request $request)
    {
        $query = Product::where('is_active', true)->with('category');

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search by name or description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by discount
        if ($request->has('has_discount') && $request->has_discount == 'true') {
            $query->where('has_discount', true);
        }

        $products = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'products' => $products
        ], 200);
    }

    /**
     * Get all products including inactive (admin)
     */
    public function adminIndex()
    {
        $products = Product::with('category')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'products' => $products
        ], 200);
    }

    /**
     * Get single product
     */
    public function show($id)
    {
        $product = Product::with('category')->find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'product' => $product
        ], 200);
    }

    /**
     * Create new product (admin)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer|exists:categories,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'has_discount' => 'nullable|boolean',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 400);
        }

        $productData = [
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock ?? 0,
            'is_active' => $request->is_active ?? true,
            'has_discount' => $request->has_discount ?? false,
            'discount_percentage' => $request->discount_percentage,
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('products', $imageName, 'public');
            $productData['image'] = $imagePath;
        }

        $product = Product::create($productData);

        return response()->json([
            'status' => 'success',
            'message' => 'Product created successfully',
            'product' => $product->load('category')
        ], 201);
    }

    /**
     * Update product (admin)
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'category_id' => 'nullable|integer|exists:categories,id',
            'name' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'has_discount' => 'nullable|boolean',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 400);
        }

        $updateData = [];
        
        foreach (['category_id', 'name', 'description', 'price', 'stock', 'is_active', 'has_discount', 'discount_percentage'] as $field) {
            if ($request->has($field)) {
                $updateData[$field] = $request->$field;
            }
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('products', $imageName, 'public');
            $updateData['image'] = $imagePath;
        }

        $product->update($updateData);

        return response()->json([
            'status' => 'success',
            'message' => 'Product updated successfully',
            'product' => $product->load('category')
        ], 200);
    }

    /**
     * Delete product (admin)
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found'
            ], 404);
        }

        // Delete image if exists
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Product deleted successfully'
        ], 200);
    }

    /**
     * Get product image
     */
    public function getImage($filename)
    {
        $path = storage_path('app/public/products/' . $filename);

        if (!file_exists($path)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Image not found'
            ], 404);
        }

        return response()->file($path);
    }
}
