<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\EnquiryController;
use App\Http\Controllers\API\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Register your API routes here. These routes are loaded by the RouteServiceProvider
| within a group that is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route to get the authenticated user's data
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Routes for authentication
Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login'); // Log in a user
    Route::post('register', 'register'); // Register a new user
    Route::get('get_user_data', 'get_user_data'); // Retrieve the authenticated user's data
    Route::delete('delete_user/{id}', 'delete_user'); // Delete a user by ID
    Route::get('show_user_data/{id}', 'show_user_data'); // Retrieve data for a specific user
    Route::post('checkEmailUniqueness', 'checkEmailUniqueness'); // Check if an email is unique
    Route::post('updatePassword/{id}', 'updatePassword'); // Update a user's password
    Route::post('sendOTP', 'sendOTP'); // Send an OTP to the user's email
    Route::post('changePassword/{id}', 'changePassword'); // Change password for a user by ID
});

// Routes for product management
Route::controller(ProductController::class)->group(function () {
    Route::get('get_data', 'get_data'); // Retrieve all product data
    Route::post('add_product', 'add_product'); // Add a new product
    Route::post('update_product/{id}', 'update_product'); // Update an existing product
    Route::post('filter_data', 'filter_data'); // Filter product data based on criteria
    Route::delete('deleteproduct/{id}', 'deleteproduct'); // Delete a product by ID
    Route::get('show_data/{id}', 'show_data'); // Retrieve data for a specific product
    Route::get('get_data_user_wise/{user_id}', 'get_data_user_wise'); // Retrieve product data for a user
    Route::get('get_home_page_data/{user_id}', 'get_home_page_data'); // Retrieve home page data for a user
    Route::get('searchProduct/{query}/{sortBy}', 'searchProduct'); // Search for products with sorting
    Route::get('product_details/{product_id}', 'product_details'); // Retrieve detailed information for a product
});

// Routes for product search with sorting
Route::get('/products/search/{query}/price_asc', [ProductController::class, 'searchProductByPriceAsc']); // Search products by ascending price
Route::get('/products/search/{query}/price_desc', [ProductController::class, 'searchProductByPriceDesc']); // Search products by descending price

// Routes for enquiry management
Route::controller(EnquiryController::class)->group(function () {
    Route::post('add_enquiry', 'add_enquiry'); // Add a new enquiry
    Route::get('getProductDetails/{id}', 'getProductDetails'); // Retrieve details for a product
    Route::get('getUserDetails/{user_id}', 'getUserDetails'); // Retrieve details for a user
    Route::get('getenquiryDetails/{user_id}', 'getenquiryDetails'); // Retrieve enquiry details for a user
    Route::get('getSingleEnquiry/{id}', 'getSingleEnquiry'); // Retrieve details for a specific enquiry
    Route::delete('delete_enquiry/{id}', 'delete_enquiry'); // Delete an enquiry by ID
});

// Route to run database migrations and clear various caches
Route::get('/migrate', function () {
    Artisan::call('migrate'); // Run migrations
    Artisan::call('cache:clear'); // Clear application cache
    Artisan::call('view:clear'); // Clear compiled view files
    Artisan::call('route:clear'); // Clear route cache
    Artisan::call('optimize:clear'); // Clear optimization cache
    return 'Migration ran successfully!';
});
