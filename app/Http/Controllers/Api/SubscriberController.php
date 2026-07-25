<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class SubscriberController extends Controller
{
    public function subscribe(Request $request)
    {
        // Validate the email
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:subscribers,email',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                ],
                422,
            );
        }

        // Create a new subscriber
        $subscriber = Subscriber::create([
            'email' => $request->email,
        ]);

        // Send email to admin
        $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
        Mail::raw($request->email . ' has subscribed to the newsletter.', function ($message) use ($adminEmail) {
            $message->to($adminEmail)->subject('New Newsletter Subscription');
        });

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Successfully subscribed to the newsletter!',
                'data' => $subscriber,
            ],
            201,
        );
    }

    public function unsubscribe(Request $request)
    {
        // Validate the email
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                ],
                422,
            );
        }

        // Find and delete the subscriber
        $subscriber = Subscriber::where('email', $request->email)->first();

        if ($subscriber) {
            $subscriber->delete();

            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Successfully unsubscribed from the newsletter!',
                ],
                200,
            );
        }

        return response()->json(
            [
                'status' => 'error',
                'message' => 'Email not found in subscribers.',
            ],
            404,
        );
    }
}
