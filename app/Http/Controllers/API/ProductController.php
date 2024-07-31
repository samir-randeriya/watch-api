<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Add a new product.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add_product(Request $request)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'brand_name' => 'required|string|max:255',
                'item_name' => 'required|string|max:255',
                'watch_pic1' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                'watch_pic2' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                'watch_pic3' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                'watch_pic4' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                'watch_pic5' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                'watch_pic6' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                'description' => 'required',
                'condition' => 'required',
                'reference_no' => 'required',
                'country' => 'required',
                'type' => 'required'
            ]);

            // Check for validation errors
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors(),
                ], 400);
            }

            // Create a new product instance and assign attributes
            $data = new Product();
            $data->brand_name = $request->brand_name;
            $data->year = $request->year;
            $data->item_name = $request->item_name;
            $data->user_id = $request->user_id;
            $data->description = $request->description;
            $data->condition = $request->condition;
            $data->price = $request->price;
            $data->reference_no = $request->reference_no;
            $data->type = $request->type;
            $data->accessories = $request->accessories;
            $data->country = $request->country;
            $data->negotiation = $request->negotiation ?? 0;
            $data->created_at = now();

            // Handle image uploads
            $this->handleImageUpload($request, $data, 'watch_pic1');
            $this->handleImageUpload($request, $data, 'watch_pic2');
            $this->handleImageUpload($request, $data, 'watch_pic3');
            $this->handleImageUpload($request, $data, 'watch_pic4');
            $this->handleImageUpload($request, $data, 'watch_pic5');
            $this->handleImageUpload($request, $data, 'watch_pic6');

            // Save the product and return a success response
            $data->save();
            return response()->json([
                'success' => true,
                'data' => $data,
                'error_flag' => 0,
                'message' => 'Product added successfully',
            ], 200);

        } catch (\Exception $e) {
            // Handle exceptions and return an error response
            return response()->json([
                'success' => false,
                'error_flag' => 1,
                'message' => 'An error occurred while processing your request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Retrieve all products with user details.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_data()
    {
        $data = Product::with('userDetail')->get();
        if ($data->isNotEmpty()) {
            return response()->json([
                'success' => true,
                'data' => $data,
                'error_flag' => 0,
                'message' => 'Get all product data successfully',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'data' => '',
                'error_flag' => 1,
                'message' => 'Products not found',
            ], 404);
        }
    }

    /**
     * Delete a product by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteproduct($id)
    {
        try {
            $data = Product::findOrFail($id);
            $data->delete();
            return response()->json([
                'success' => true,
                'data' => $data,
                'error_flag' => 0,
                'message' => 'Product deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'error_flag' => 1,
                'message' => 'An error occurred while deleting the product.',
            ], 500);
        }
    }

    /**
     * Retrieve products by user ID.
     *
     * @param int $user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_data_user_wise($user_id)
    {
        $products = Product::where('user_id', $user_id)->get();
        if ($products->isNotEmpty()) {
            return response()->json([
                'success' => true,
                'data' => $products,
                'error_flag' => 0,
                'message' => 'Get all product data for user successfully',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'data' => '',
                'error_flag' => 1,
                'message' => 'Products not found',
            ], 404);
        }
    }

    /**
     * Retrieve home page data for a user.
     *
     * @param int $user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_home_page_data($user_id)
    {
        $products = Product::with('userDetail')->where('user_id', $user_id)->get();
        if ($products->isNotEmpty()) {
            return response()->json([
                'success' => true,
                'data' => $products,
                'error_flag' => 0,
                'message' => 'Get home page product data for user successfully',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'data' => '',
                'error_flag' => 1,
                'message' => 'Products not found',
            ], 404);
        }
    }

    /**
     * Show product details by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show_data($id)
    {
        $data = Product::find($id);
        if ($data) {
            return response()->json([
                'success' => true,
                'data' => $data,
                'error_flag' => 0,
                'message' => 'Get product data successfully',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'data' => '',
                'error_flag' => 1,
                'message' => 'Product not found',
            ], 404);
        }
    }

    /**
     * Update a product by ID.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update_product(Request $request, $id)
    {
        try {
            $data = Product::find($id);
            if ($data) {
                // Validate the request data
                $validator = Validator::make($request->all(), [
                    'brand_name' => 'required|string|max:255',
                    'item_name' => 'required|string|max:255',
                    'watch_pic1' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                    'watch_pic2' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                    'watch_pic3' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                    'watch_pic4' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                    'watch_pic5' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                    'watch_pic6' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                    'description' => 'required',
                    'condition' => 'required',
                    'country' => 'required',
                    'reference_no' => 'required',
                ]);

                // Check for validation errors
                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => $validator->errors(),
                    ], 400);
                }

                // Update fields based on validated data
                $data->brand_name = $request->brand_name;
                $data->year = $request->year;
                $data->item_name = $request->item_name;
                $data->user_id = $request->user_id;
                $data->description = $request->description;
                $data->condition = $request->condition;
                $data->price = $request->price;
                $data->reference_no = $request->reference_no;
                $data->type = $request->type;
                $data->accessories = $request->accessories;
                $data->country = $request->country;
                $data->negotiation = $request->negotiation ?? 0;
                $data->updated_at = now();

                // Handle image uploads
                $this->handleImageUpload($request, $data, 'watch_pic1');
                $this->handleImageUpload($request, $data, 'watch_pic2');
                $this->handleImageUpload($request, $data, 'watch_pic3');
                $this->handleImageUpload($request, $data, 'watch_pic4');
                $this->handleImageUpload($request, $data, 'watch_pic5');
                $this->handleImageUpload($request, $data, 'watch_pic6');

                // Save the updated product and return a success response
                $data->save();
                return response()->json([
                    'success' => true,
                    'data' => $data,
                    'error_flag' => 0,
                    'message' => 'Product updated successfully',
                ], 200);

            } else {
                return response()->json([
                    'success' => false,
                    'error_flag' => 1,
                    'message' => 'Product not found',
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error_flag' => 1,
                'message' => 'An error occurred while updating the product.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle image uploads for a product.
     *
     * @param Request $request
     * @param Product $product
     * @param string $imageField
     * @return void
     */
    protected function handleImageUpload(Request $request, Product $product, $imageField)
    {
        if ($request->hasFile($imageField)) {
            $file = $request->file($imageField);
            $filePath = $file->store('images', 'public');
            $product->{$imageField} = $filePath;
        }
    }
}
