<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enquiry;
use App\Models\Product;
use App\Models\User_details;
use Illuminate\Support\Facades\Validator;

class enquiryController extends Controller
{
    /**
     * Add a new enquiry.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add_enquiry(Request $request)
    {
        try {
            // Retrieve product and user details to validate existence
            $productDetails = $this->getProductDetails($request->watch_id);
            $userDetails = $this->getUserDetails($request->user_id);

            // Check if both product and user details are retrieved successfully
            if (isset($productDetails->original['message']) || isset($userDetails->original['message'])) {
                return response()->json([
                    'success' => false,
                    'error_flag' => 1,
                    'message' => 'Invalid product or user details.',
                ], 404);
            }

            // Create a new Enquiry instance and populate fields
            $data = new Enquiry();
            $data->watch_id = $request->watch_id;
            $data->user_id = $request->user_id;
            $data->price = $request->price;
            $data->created_at = now();
            $data->save();

            // Return success response with enquiry and related details
            $response = [
                'success' => true,
                'data' => $data,
                'productDetails' => $productDetails,
                'userDetails' => $userDetails,
                'error_flag' => 0,
                'message' => 'Enquiry added successfully',
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            // Handle exceptions and return error response
            $response = [
                'success' => false,
                'error_flag' => 1,
                'message' => 'An error occurred while processing your request.',
                'error' => $e->getMessage(),
            ];

            return response()->json($response, 500); // HTTP status code for Internal Server Error
        }
    }

    /**
     * Retrieve product details by watch ID.
     *
     * @param int $watch_id
     * @return \Illuminate\Http\JsonResponse|Product
     */
    public function getProductDetails($watch_id)
    {
        $product = Product::where('id', $watch_id)->first();

        if ($product) {
            // Product details found
            return $product;
        } else {
            // Product not found
            return response()->json(['message' => 'Product not found.'], 404);
        }
    }

    /**
     * Retrieve user details by user ID.
     *
     * @param int $user_id
     * @return \Illuminate\Http\JsonResponse|User_details
     */
    public function getUserDetails($user_id)
    {
        $user = User_details::where('id', $user_id)->first();

        if ($user) {
            // User details found
            return $user;
        } else {
            // User not found
            return response()->json(['message' => 'User not found'], 404);
        }
    }

    /**
     * Retrieve all enquiries for a user based on their products.
     *
     * @param int $user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEnquiryDetails($user_id)
    {
        // Retrieve products for the user
        $products = Product::where('user_id', $user_id)->get();

        if ($products->isEmpty()) {
            return response()->json(['message' => 'Products not found'], 404);
        }

        // Extract product IDs and retrieve associated enquiries
        $productIds = $products->pluck('id');
        $enquiries = Enquiry::whereIn('watch_id', $productIds)->with('user')->get();

        if ($enquiries->isEmpty()) {
            return response()->json(['message' => 'Enquiries not found'], 404);
        }

        // Extract unique user IDs from enquiries and retrieve associated user details
        $userIds = $enquiries->pluck('user_id')->unique();
        $users = User_details::whereIn('id', $userIds)->get();

        if ($users->isEmpty()) {
            return response()->json(['message' => 'Users not found'], 404);
        }

        // Return detailed enquiry data
        return response()->json([
            'enquiries' => $enquiries,
            'products' => $products,
            'users' => $users
        ]);
    }

    /**
     * Retrieve a single enquiry by its ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSingleEnquiry($id)
    {
        try {
            // Retrieve enquiry by ID
            $enquiry_data = Enquiry::where('id', $id)->get();
            $watch_id = $enquiry_data->pluck('watch_id');
            $product_data = Product::where('id', $watch_id)->get();
            $user_id = $enquiry_data->pluck('user_id');
            $user_data = User_details::where('id', $user_id)->get();

            $final_data = [
                'product' => $product_data,
                'enq_data' => $enquiry_data,
                'user_data' => $user_data
            ];

            // Return success response with enquiry details
            return response()->json([
                'success' => true,
                'message' => 'Get Enquiry successfully',
                'data' => $final_data,
                'error_flag' => 0
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Handle model not found exception
            return response()->json([
                'success' => false,
                'error_flag' => 1,
                'message' => 'Enquiry not found',
            ], 404);
        } catch (\Exception $e) {
            // Handle general exceptions
            return response()->json([
                'success' => false,
                'error_flag' => 1,
                'message' => 'An error occurred while processing your request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete an enquiry by its ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete_enquiry($id)
    {
        try {
            // Find and delete the enquiry
            $data = Enquiry::findOrFail($id);
            $data->delete();

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Enquiry deleted successfully',
                'data' => $data,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Handle model not found exception
            return response()->json([
                'success' => false,
                'error_flag' => 1,
                'message' => 'Enquiry not found',
            ], 404);
        } catch (\Exception $e) {
            // Handle general exceptions
            return response()->json([
                'success' => false,
                'error_flag' => 1,
                'message' => 'An error occurred while deleting the enquiry.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
