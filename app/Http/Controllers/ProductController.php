<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showProduct()
{
    // Retrieve data from the database if needed
    $products = Product::all();

    // Sort the products array by product name and unit in alphanumeric order
    $sortedProducts = $products->sortBy(function ($product) {
        return $product->prod_name . $product->unit;
    });

    // Pass the data to the Blade view and render it
    return view('pages.show-product', ['sortedProducts' => $sortedProducts]);
}


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createProduct()
    {

        return view('pages.create-product');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeProduct(Request $request)
    {
        // Validate the request data
        $validator = $request->validate([
            // product name validation
            'prod_name' => ['required', 'min:4', 'max:20', 'string', 'unique:products'],
            // unit validation
            'unit' => ['required','string','min:4', 'max:20'],
            // price validation
            'price' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
            // expiration date validation
            'expiration_date' => ['required','date'],
            // available product validation
            'available' => ['required', 'integer', 'min:1', 'max:100000'],
            // product image validation
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:50048'],
            // Define validation rules for other fields.
        ]);

        // Handle the image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('upload_images'), $imageName);
        } else {
            $imageName = null;
        }

        // Create a new product
        $product = new Product([
            'prod_name' => $request->input('prod_name'),
            'unit' => $request->input('unit'),
            'price' => $request->input('price'),
            'expiration_date' => $request->input('expiration_date'),
            'available' => $request->input('available'),
            'image' => $imageName,
        ]);

        $product->save();

        // Send a JSON response on successful validation
        return new JsonResponse(['message' => 'Product successfully stored']);
    }

    // At the end of your controller class, add the following method to handle validation errors
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        return new JsonResponse(['errors' => $validator->errors()], 422);
    }


    public function edit($id)
    {
        $product = Product::find($id);
    
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
    
        return response()->json(['product' => $product]);
    }
    

    public function update(Request $request)
    {
        // Validate the request data
        $request->validate([
            'product_id' => ['required', 'exists:products,id'], // Check if the product exists
            // product name validation
            'prod_name' => ['required'],
            // unit validation
            'unit' => ['required'],
            // price validation
            'price' => ['required'],
            // expiration date validation
            'expiration_date' => ['required'],
            // available product validation
            'available' => ['required'],
            // product image validation
            'image' =>  ['image',
                'mimes:jpeg,png,jpg,HEIC',
                'max:50048',
            ],  
            // Example for updating the product name
            // Add validation rules for other fields you want to update
        ]);
        try {
            // Find the product by ID
            $product = Product::findOrFail($request->input('product_id'));
    
            // Update the product properties based on the request data
            $product->prod_name = $request->input('prod_name');
            $product->unit = $request->input('unit');
            $product->price = $request->input('price');
            $product->expiration_date = $request->input('expiration_date');
            $product->available = $request->input('available');
    
            // Handle the uploaded image
            if ($request->hasFile('image')) {
                // Get the file from the request
                $image = $request->file('image');
                
                // Generate a unique name for the image
                $imageName = time() . '.' . $image->getClientOriginalExtension();
    
                // Move the uploaded image to the storage directory
                $image->move(public_path('upload_images'), $imageName);
    
                // Update the product's image attribute with the new file name
                $product->image = $imageName;
            }
    
            // Save the updated product
            $product->save();
    
            // Return a JSON response indicating success
            return response()->json(['message' => 'Product successfully updated']);
        } catch (\Exception $e) {
            // Handle any exceptions that may occur during the update
            return response()->json(['error' => 'Error updating product'], 500); // HTTP status code 500 for server error
        }
    }
    
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
             // Delete the associated image
       // Delete the associated image
        if ($product->image) {
            $imagePath = public_path('upload_images/' . $product->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
            $product->delete();
            return response()->json(['message' => 'Product deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error deleting product'], 500);
        }
    }
    
}
