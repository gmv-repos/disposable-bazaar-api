<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactUsController extends Controller
{
    public function contactUs(Request $request)
    {
        // Validate request
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        // Get admin email from env
        $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');

        // Prepare email body
        $emailBody = "
        New Contact Us Message:

        Name: {$request->name}
        Phone: {$request->phone}
        Email: {$request->email}
        Message:
        {$request->message}
        ";

        // Send email
        Mail::raw($emailBody, function ($message) use ($adminEmail) {
            $message->to($adminEmail)->subject('New Contact Us Message');
        });

        return response()->json(
            [
                'success' => true,
                'message' => 'Your message has been sent successfully!',
            ],
            200,
        );
    }
}
