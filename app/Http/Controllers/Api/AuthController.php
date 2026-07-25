<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignupRequest;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\ContactUs;
use App\Models\Inquiry;
use App\Models\Review;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgetPasswordMail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Intervention\Image\Facades\Image;
use DB;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function handleGoogleCallback(Request $request)
    {
        $idToken = $request->input('id_token');

        // Fetch user info from Google
        $tokenInfo = $this->getGoogleUserInfo($idToken);

        if (!$tokenInfo) {
            return response()->json(['status' => 'error', 'message' => 'Invalid token'], 400);
        }

        // Check if the user already exists
        $user = User::where('email', $tokenInfo['email'])->where('google_id', $tokenInfo['sub'])->first();

        // If the user does not exist, create a new one
        if (!$user) {
            $user = User::create([
                'name' => $tokenInfo['name'] ?? null,
                'email' => $tokenInfo['email'] ?? null,
                'password' => Hash::make('12345678'), // Default password for the created user
                'google_id' => $tokenInfo['sub'] ?? null,
            ]);
        }

        // Authenticate the user
        $credentials = ['email' => $tokenInfo['email'], 'password' => '12345678'];

        if (Auth::attempt($credentials)) {
            $accessToken = $user->createToken('MyApp')->plainTextToken;
            return response()->json([
                'status' => 'success',
                'message' => 'User logged in successfully',
                'data' => [
                    'user' => $user,
                    'access_token' => $accessToken,
                ],
            ]);
        } else {
            return response()->json([
                'status' => 'warning',
                'message' => 'Login failed',
                'data' => null,
            ]);
        }
    }

    private function getGoogleUserInfo($idToken)
    {
        // Implement token info retrieval from Google
        $url = 'https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=' . $idToken;
        $response = Http::get($url);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    public function handleFacebookCallback(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'access_token' => 'required|string',
        ]);

        // Get the access token from the request
        $accessToken = $request->input('access_token');

        // Fetch user info from Facebook using the access token
        $facebookUser = $this->getFacebookUserInfo($accessToken);

        if (!$facebookUser) {
            return response()->json(['status' => 'error', 'message' => 'Invalid token'], 400);
        }

        // Check if the user already exists
        $user = User::where('email', $facebookUser->email)->where('provider_id', $facebookUser->id)->first();

        // If the user does not exist, create a new one
        if (!$user) {
            $user = User::create([
                'name' => $facebookUser->name ?? null,
                'email' => $facebookUser->email ?? null,
                'password' => Hash::make('12345678'), // Default password for the created user
                'provider_id' => $facebookUser->id ?? null,
            ]);
        }

        // Authenticate the user
        $credentials = ['email' => $facebookUser->email, 'password' => '12345678'];

        if (Auth::attempt($credentials)) {
            $accessToken = $user->createToken('MyApp')->plainTextToken;
            return response()->json([
                'status' => 'success',
                'message' => 'User logged in successfully',
                'data' => [
                    'user' => $user,
                    'access_token' => $accessToken,
                ],
            ]);
        } else {
            return response()->json([
                'status' => 'warning',
                'message' => 'Login failed',
                'data' => null,
            ]);
        }
    }

    private function getFacebookUserInfo($accessToken)
    {
        // Implement token info retrieval from Facebook
        $url = 'https://graph.facebook.com/me?fields=id,name,email&access_token=' . $accessToken;
        $response = Http::get($url);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    public function updateUser(Request $request): JsonResponse
    {
        try {
            $userId = Auth::user()->id;
            $user = User::find($userId);

            if (is_null($user)) {
                return response()->json(
                    [
                        'status' => 'warning',
                        'message' => 'Un Authorized',
                    ],
                    404,
                );
            }

            Log::info('Request data: ', $request->all());

            // Validate the incoming request data
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'phone' => 'sometimes|string|max:15',
                'address' => 'sometimes|string|max:255',
                'photo' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'Validation errors',
                        'errors' => $validator->errors(),
                    ],
                    422,
                );
            }

            if ($request->has('name')) {
                $user->name = $request->input('name');
            }
            if ($request->has('phone')) {
                $user->phone = $request->input('phone');
            }
            if ($request->has('address')) {
                $user->address = $request->input('address');
            }

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('profile_images', $fileName, 'public'); // Store the file
                $user->photo = $filePath;
            }

            $user->save();

            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'User updated successfully',
                    'data' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'phone' => $user->phone,
                        'address' => $user->address,
                        'photo' => $user->photo,
                        'email' => $user->email,
                    ],
                ],
                200,
            );
        } catch (\Throwable $th) {
            Log::error('Update error: ' . $th->getMessage());
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'An unexpected error occurred.',
                    'data' => null,
                ],
                500,
            );
        }
    }

    public function updateUserDetails(Request $request)
    {
        // Get the currently authenticated user
        $user = Auth::user();

        // Check if the user is found
        if (!$user) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'User not found',
                    'data' => null,
                ],
                404,
            );
        }

        // Update user fields using mass assignment
        $user->fill($request->only('name', 'phone', 'address'));

        // Handle photo upload if provided
        if ($request->hasFile('photo')) {
            // Store the photo in the public storage and get the path
        }

        // Save the user details
        $user->save();

        // Return a successful response
        return response()->json([
            'status' => 'success',
            'message' => 'User details updated successfully',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'photo' => $user->photo,
            ],
        ]);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        if (!Auth::attempt($credentials)) {
            return response([
                'message' => 'Provided Email Address or Password is Incorrect',
                'status' => 'error',
            ]);
        }
        /** @var User $user */
        $user = Auth::user();
        $token = $user->createToken('main')->plainTextToken;
        return response()->json(
            [
                'status' => 200,
                'message' => 'User logged in successfully',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ],
            ],
            200,
        );
    }

    public function signup(SignupRequest $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|regex:/^[a-zA-Z0-9\s\-\_\@\#\$\%\^\&\*\(\)]+$/',
                'email' => 'required|email',
                'password' => ['required', 'string', 'min:8'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'warning',
                    'message' => $validator->errors()->first(),
                ]);
            }

            $input = $request->all();

            if (User::where('email', $input['email'])->exists()) {
                Log::info('Email already exists: ' . $input['email']);
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'This email already exists',
                    ],
                    422,
                );
            }

            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]);

            $token = $user->createToken('main')->plainTextToken;

            Auth::login($user);

            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'User Registered Successfully',
                    'data' => [
                        'user' => $user,
                        'token' => $token,
                    ],
                ],
                200,
            );
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'msg' => 'Signup failed'], 500);
        }
    }

    public function logout(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        $user->currentAccessToken()->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'User logged out Successfully',
        ]);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'currentPass' => ['required', 'string', 'min:8'],
            'newPass' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'warning',
                'message' => $validator->errors()->first(),
            ]);
        }

        // Get the authenticated user
        $user = $request->user();

        // Check if the provided current password matches the stored password
        if (!Hash::check($request->currentPass, $user->password)) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => 'The current password is incorrect',
                ],
                400,
            );
        }

        // Hash the new password and update it in the database
        $user->password = Hash::make($request->newPass);
        $user->save();

        return response()->json(
            [
                'status' => 200,
                'message' => 'Password Successfully Changed',
            ],
            200,
        );
    }

    public function forgetPassword(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()]);
        }

        // Get the user
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found']);
        }

        $code = rand(100000, 999999);
        $user->verification_code = $code;
        $user->save();
        Mail::to($user->email)->send(new ForgetPasswordMail($code));

        return response()->json([
            'status' => 'success',
            'message' => 'Verification code has been Sent to your email address',
        ]);
    }

    public function resetPassword(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()]);
        }

        // Find the user
        $user = User::where('email', $request->email)->first();

        // Check OTP
        if (!$user || $request->otp == $user->otp) {
            return response()->json(['status' => 'error', 'message' => 'Invalid OTP']);
        }

        // Reset the password
        $user->password = Hash::make($request->password);
        $user->verification_code = null;
        $user->save();

        return response()->json(['status' => 'success', 'message' => 'Password reset successfully']);
    }

    public function contact_add(Request $request)
    {
        //DB::beginTransaction();
        //try{

        $contactDetail = ContactUs::create($request->all());
        $success = [
            'status' => 200,
            'message' => 'Successfully Created',
            'data' => $contactDetail,
        ];
        return response()->json($success);

        // } catch (\Throwable $e) {
        //     DB::rollBack();
        //     return response()->json('Internal error', 404);
        // }
    }

    public function inquiry_add(Request $request)
    {
        //  DB::beginTransaction();
        //  try{
        $data = $request->all();
        $data['logo_design'] = $this->inquiryImage($request->file('logo_design'));
        $inquiryDetail = Inquiry::create($data);
        $success = [
            'status' => 200,
            'message' => 'Successfully Created',
            'data' => $inquiryDetail,
        ];
        return response()->json($success);

        // } catch (\Throwable $e) {
        //     DB::rollBack();
        //     return response()->json('Internal error', 404);
        // }
    }

    public function inquiryImage($image)
    {
        if ($image instanceof \Illuminate\Http\UploadedFile) {
            // Get MIME type and file extension
            $mimeType = $image->getClientMimeType();
            $ext = $image->getClientOriginalExtension();

            // Generate unique file name
            $filename = 'inquiry_images-' . time() . rand(1000, 9999) . '.' . $ext;

            // Define the path in storage
            $path = 'inquiry_images/' . $filename;

            // Create an instance of Image
            $imageInstance = \Image::make($image)
                ->resize(400, 400) // Resize image
                ->brightness(8) // Adjust brightness
                ->contrast(11) // Adjust contrast
                ->sharpen(5) // Sharpen image
                ->encode('webp', 70); // Encode image as WebP

            // Save image to storage
            \Storage::put($path, (string) $imageInstance);

            // Return the URL to access the image
            return \Storage::url($path);
        }

        return null; // Return null if image is not provided
    }

    public function reviewImage($image)
    {
        if ($image instanceof \Illuminate\Http\UploadedFile) {
            // Get MIME type and file extension
            $mimeType = $image->getClientMimeType();
            $ext = $image->getClientOriginalExtension();

            // Generate unique file name
            $filename = 'review_images-' . time() . rand(1000, 9999) . '.' . $ext;

            // Define the path in storage
            $path = 'review_images/' . $filename;

            // Create an instance of Image
            $imageInstance = \Image::make($image)
                ->resize(400, 400) // Resize image
                ->brightness(8) // Adjust brightness
                ->contrast(11) // Adjust contrast
                ->sharpen(5) // Sharpen image
                ->encode('webp', 70); // Encode image as WebP

            // Save image to storage
            \Storage::put($path, (string) $imageInstance);

            // Return the URL to access the image
            return \Storage::url($path);
        }

        return null; // Return null if image is not provided
    }
}
