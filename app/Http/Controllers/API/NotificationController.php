<?php

namespace App\Http\Controllers\API;

use App\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function notifications() {
        Notification::where('user_id', Auth::id())->update(['seen' => 1]);
        $notifications = Notification::where('user_id', Auth::id())->latest()->paginate(30);
        return $notifications;
    }

}
