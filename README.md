# Laravel API Documentation

This documentation covers the API endpoints available in the Laravel application. The API is designed for user authentication, product management, and enquiry handling.

## Table of Contents

1. [Authentication Routes](#authentication-routes)
2. [Product Management Routes](#product-management-routes)
3. [Enquiry Management Routes](#enquiry-management-routes)
4. [Miscellaneous Routes](#miscellaneous-routes)

## Authentication Routes

| Method | Endpoint                        | Description                              |
|--------|---------------------------------|------------------------------------------|
| POST   | `/login`                        | Log in a user                            |
| POST   | `/register`                     | Register a new user                      |
| GET    | `/get_user_data`                | Retrieve the authenticated user's data   |
| DELETE | `/delete_user/{id}`             | Delete a user by ID                      |
| GET    | `/show_user_data/{id}`          | Retrieve data for a specific user        |
| POST   | `/checkEmailUniqueness`         | Check if an email is unique              |
| POST   | `/updatePassword/{id}`          | Update a user's password                 |
| POST   | `/sendOTP`                      | Send an OTP to the user's email          |
| POST   | `/changePassword/{id}`          | Change password for a user by ID         |

## Product Management Routes

| Method | Endpoint                               | Description                                       |
|--------|----------------------------------------|---------------------------------------------------|
| GET    | `/get_data`                            | Retrieve all product data                         |
| POST   | `/add_product`                         | Add a new product                                 |
| POST   | `/update_product/{id}`                 | Update an existing product                        |
| POST   | `/filter_data`                         | Filter product data based on criteria             |
| DELETE | `/deleteproduct/{id}`                  | Delete a product by ID                            |
| GET    | `/show_data/{id}`                      | Retrieve data for a specific product              |
| GET    | `/get_data_user_wise/{user_id}`        | Retrieve product data for a user                  |
| GET    | `/get_home_page_data/{user_id}`        | Retrieve home page data for a user                |
| GET    | `/searchProduct/{query}/{sortBy}`      | Search for products with sorting                  |
| GET    | `/product_details/{product_id}`        | Retrieve detailed information for a product       |
| GET    | `/products/search/{query}/price_asc`   | Search products by ascending price                |
| GET    | `/products/search/{query}/price_desc`  | Search products by descending price               |

## Enquiry Management Routes

| Method | Endpoint                        | Description                                   |
|--------|---------------------------------|-----------------------------------------------|
| POST   | `/add_enquiry`                  | Add a new enquiry                             |
| GET    | `/getProductDetails/{id}`       | Retrieve details for a product                |
| GET    | `/getUserDetails/{user_id}`     | Retrieve details for a user                   |
| GET    | `/getenquiryDetails/{user_id}`  | Retrieve enquiry details for a user           |
| GET    | `/getSingleEnquiry/{id}`        | Retrieve details for a specific enquiry       |
| DELETE | `/delete_enquiry/{id}`          | Delete an enquiry by ID                       |

## Miscellaneous Routes

| Method | Endpoint      | Description                                          |
|--------|---------------|------------------------------------------------------|
| GET    | `/migrate`    | Run database migrations and clear various caches     |
