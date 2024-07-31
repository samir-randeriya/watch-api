<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Individual;
use App\Models\User;
use App\Models\User_details;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /**
     * Register a new user or company.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // Validate the request input
        $validator = Validator::make($request->all(), [
            'user_name' => 'nullable',
            'email' => 'required|email|unique:individuals,email|unique:companies,email',
            'contact_number' => 'nullable',
            'address' => 'nullable',
            'company_name' => 'nullable',
            'company_number' => 'nullable',
            'company_address' => 'nullable',
            'password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
            ],
            'c_password' => 'required|same:password',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least :min characters.',
            'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, one digit, and one special character.',
            'c_password.required' => 'The confirm password field is required.',
            'c_password.same' => 'The confirm password and password must match.',
            'profile_photo.image' => 'The profile photo must be an image.',
            'profile_photo.mimes' => 'The profile photo must be a file of type: :values.',
            'profile_photo.max' => 'The profile photo may not be greater than :max kilobytes.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
        ]);

        // If validation fails, return the errors
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors(),
            ];
            return response()->json($response, 400);
        }

        // Hash the password and create a new user
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User_details::create($input);

        // Handle profile photo upload
        $this->handleImageUpload($request, $user, 'profile_photo');
        $user->update(['created_at' => now()]);

        // Generate and return a token for the user
        $success['token'] = $user->createToken('MyApp')->plainTextToken;
        $response = [
            'success' => true,
            'message' => 'User registered successfully',
        ];

        return response()->json($response, 200);
    }

    /**
     * Handle the upload of a profile photo.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User_details $user
     * @param string $fieldName
     */
    private function handleImageUpload(Request $request, User_details $user, string $fieldName)
    {
        if ($request->hasFile($fieldName)) {
            $imagePath = $request->file($fieldName)->store('profile_photo', 'public');
            $user->$fieldName = $imagePath;
        }
    }

    /**
     * Send an OTP to the given email address.
     *
     * @param string $email
     * @param int $otp
     */
    private function sendOtpByEmail($email, $otp)
    {
        $data = ['otp' => $otp];
        Mail::send([], $data, function ($message) use ($email, $data) {
            $message->to($email)
                ->subject('Your OTP')
                ->setBody('<p>Verify Account OTP: <strong>' . $data['otp'] . '</strong></p>', 'text/html');
        });
    }

    /**
     * Generate a 6-digit OTP.
     *
     * @return int
     */
    private function generateOTP()
    {
        return rand(1000, 9999);
    }

    /**
     * Login a user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            $userDetail = User_details::where('email', $credentials['email'])->first();

            if ($userDetail && Hash::check($credentials['password'], $userDetail->password)) {
                $data = $userDetail->toArray();
                // Update the user's device token and user_id
                $deviceToken = $request->input('device_token');
                $user_id = $request->input('user_id');
                $userDetail->device_token = $deviceToken;
                $userDetail->user_id = $user_id;
                $userDetail->save();

                $response = [
                    'success' => true,
                    'message' => 'Login successful',
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Login failed',
                ];
            }
        } catch (\Exception $e) {
            $response = [
                'message' => 'Error found while fetching data',
                'error' => $e->getMessage()
            ];
        }

        return response()->json($response);
    }

    /**
     * Check if the email is unique across individuals and companies.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkEmailUniqueness(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:individuals,email|unique:companies,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 400);
        }

        $email = $request->email;
        $exists = User_details::where('email', $email)->first();
        $user_id = $exists ? $exists->id : null;

        $response = [
            'success' => true,
            'data' => [
                'email' => $email,
                'unique' => !$exists,
                'user_id' => $user_id,
            ],
            'message' => $exists ? 'Email already exists.' : 'Email is unique.',
        ];

        return response()->json($response, 200);
    }

    /**
     * Show user data by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show_user_data($id)
    {
        $data = User_details::find($id);

        if ($data) {
            $response = [
                'success' => true,
                'data' => $data,
                'error_flag' => 0,
                'message' => 'Get the User data successfully',
            ];
            return response()->json($response, 200);
        } else {
            $response = [
                'success' => false,
                'data' => '',
                'error_flag' => 1,
                'message' => 'User not found',
            ];
            return response()->json($response, 404);
        }
    }

    /**
     * Update the password for a user.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(Request $request, $id)
    {
        try {
            $user = User_details::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error_flag' => 1,
                    'message' => 'User not found',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'password' => [
                    'required',
                    'min:8',
                    'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
                ],
                'c_password' => 'required|same:password',
            ], [
                'password.*' => 'Invalid password format or mismatch.',
                'c_password.required' => 'The confirm password field is required.',
                'c_password.same' => 'The confirm password and password must match.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error_flag' => 1,
                    'message' => $validator->errors(),
                ], 400);
            }

            $user->password = bcrypt($request->password);
            $user->save();

            return response()->json([
                'success' => true,
                'data' => $user,
                'error_flag' => 0,
                'message' => 'Password updated successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Retrieve all user data.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_user_data()
    {
        $data = User_details::all();
        $response = [
            'success' => true,
            'data' => $data,
            'message' => 'Get all Users data successfully',
        ];
        return response()->json($response, 200);
    }

    /**
     * Delete a user by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete_user($id)
    {
        try {
            $data = User_details::findOrFail($id);
            $data->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
                'data' => $data,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error_flag' => 1,
                'message' => 'User not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send an OTP to the specified email address.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 400);
        }

        $this->otp = $this->generateOTP();
        $this->sendOtpByEmail($request->email, $this->otp);

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully.',
        ]);
    }

    /**
     * Change the user's password if the entered password is correct.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function chnagePassword(Request $request, $id)
    {
        $enteredPassword = $request->enteredPassword;
        $newPassword = $request->newPassword;
        $user = User_details::find($id);

        if ($user) {
            $password = $user->password;
            if (Hash::check($enteredPassword, $password)) {
                $user->password = Hash::make($newPassword);
                $user->save();
                return response()->json([
                    'success' => true,
                    'error_flag' => 0,
                    'message' => 'Password updated successfully',
                    'data' => $user
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'error_flag' => 1,
                    'message' => 'Old password does not match',
                    'data' => null,
                ], 500);
            }
        } else {
            return response()->json([
                'success' => false,
                'error_flag' => 1,
                'message' => 'User not found',
            ], 404);
        }
    }
}
