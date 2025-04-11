<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;  // Assuming you're using the Product model

class ProductController extends Controller
{
    public function index(Request $request)
{
    $farmer_id = $request->query('farmer_id');  // Get the farmer_id from the query parameter

    // Fetch products that belong to the specified farmer_id
    $products = Product::where('farmer_id', $farmer_id)->get();

    return response()->json($products);
}


public function store(Request $request)
{
    // Validation
    $validated = $request->validate([
        'farmer_id' => 'required|integer',
        'name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'stock' => 'required|integer',
        'status' => 'required|string|in:Available,Low Stock,Out of Stock',
        'image' => 'nullable|string',  // Expecting base64 image string
        'description' => 'nullable|string',
    ]);

    // If image is a base64 string, decode it and store it as a file
    if (isset($validated['image'])) {
        $imageData = base64_decode($validated['image']);  // Decode base64 string
        $imageName = uniqid() . '.png';  // Generate unique name for the image
        $path = storage_path('app/public/images/' . $imageName);  // Set the path to store the image
        file_put_contents($path, $imageData);  // Save the image to the server
        $validated['image'] = 'images/' . $imageName;  // Save the image path to the database
    } else {
        $validated['image'] = null;  // If no image, set it to null
    }

    // Create Product
    $product = Product::create($validated);

    return response()->json([
        'message' => 'Product added successfully!',
        'product' => $product,
    ], 201);
}
public function destroy($id)
{
    // Find the product by ID
    $product = Product::find($id);

    if ($product) {
        // Delete the product
        $product->delete();
        return response()->json([
            'message' => 'Product deleted successfully!',
        ], 200);
    } else {
        return response()->json([
            'message' => 'Product not found.',
        ], 404);
    }
}

public function update(Request $request, $id)
{
    // Validation
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'stock' => 'required|integer',
        'status' => 'required|string|in:Available,Low Stock,Out of Stock',
        'image' => 'nullable|string',  // Expecting base64 image string
        'description' => 'nullable|string',
    ]);

    // Find the product to be updated
    $product = Product::find($id);
    if (!$product) {
        return response()->json([
            'message' => 'Product not found.',
        ], 404);
    }

    // If image is a base64 string, decode it and store it as a file
    if (isset($validated['image']) && !empty($validated['image'])) {
        $imageData = base64_decode($validated['image']);  // Decode base64 string
        $imageName = uniqid() . '.png';  // Generate unique name for the image
        $path = storage_path('app/public/images/' . $imageName);  // Set the path to store the image
        file_put_contents($path, $imageData);  // Save the image to the server

        // Save the image path to the database
        $validated['image'] = 'images/' . $imageName;  // Correct the path to use the storage link
    }

    // Update Product
    $product->update($validated);

    return response()->json([
        'message' => 'Product updated successfully!',
        'product' => $product,
    ], 200);
}
public function getProductsByUser($farmerId)
{
    $products = Product::where('farmer_id', $farmerId)->get();
    return response()->json($products);
}




}
