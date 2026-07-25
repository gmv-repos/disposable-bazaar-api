<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserShippingBillingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function userAddress(Request $request)
    {
        $userId = Auth::user()->id;

        // Validate request data
        $validatedData = $request->validate([
            'shipping_email' => 'required|email',
            'shipping_first_name' => 'required|string|max:255',
            'shipping_last_name' => 'required|string|max:255',
            'shipping_address' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:255',
            'shipping_country' => 'required|string|max:255',
            'shipping_zip' => 'required|string|max:10',
            'shipping_phone' => 'required|string|max:20',
            'shipping_state' => 'required|string|max:255',
            'shipping_division' => 'nullable|string|max:255',
            'shipping_district' => 'nullable|string|max:255',

            'billing_address' => 'required|string|max:255',
            'billing_city' => 'required|string|max:255',
            'billing_country' => 'required|string|max:255',
            'billing_zip' => 'required|string|max:10',
            'billing_state' => 'required|string|max:255',
            'billing_first_name' => 'required|string|max:255',
            'billing_last_name' => 'required|string|max:255',
            'billing_email' => 'required|email',
            'billing_phone' => 'required|string|max:20',
            'billing_division' => 'nullable|string|max:255',
            'billing_district' => 'nullable|string|max:255',
        ]);

        // Check if user info already exists
        $userInfo = UserShippingBillingAddress::where('user_id', $userId)->first();

        if ($userInfo) {
            // Update existing user info
            $userInfo->update($validatedData);
            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Address edited successfully',
                    'data' => null,
                ],
                200,
            );
        } else {
            // Create new user info
            $validatedData['user_id'] = $userId;
            UserShippingBillingAddress::create($validatedData);
            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Address created successfully',
                    'data' => null,
                ],
                200,
            );
        }
    }

    function userAddressGet()
    {
        $userinfo = UserShippingBillingAddress::where('user_id', Auth::user()->id)->first();

        return response()->json([
            'status' => 'success',
            'message' => 'Sipping Address Details Retrieved successfully',
            'data' => $userinfo,
        ]);
    }
}
