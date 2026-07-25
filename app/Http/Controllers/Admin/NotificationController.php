<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\ProductVariant;
class NotificationController extends Controller
{
    public function markNotificationAsRead(Request $request)
    {
        $notification = Notification::find($request->notification_id);
        if ($notification) {
            $notification->is_read = 1;
            $notification->save();
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error'], 400);
    }
}
