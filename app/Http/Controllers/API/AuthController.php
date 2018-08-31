<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public $successStatus = 200;
    public $failedStatus = 401;

    public function login()
    {
        sleep(2);
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('connectToken')->accessToken;
            return response()->json(['success' => $success], $this->successStatus);
        } elseif (Auth::attempt(['username' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('connectToken')->accessToken;
            return response()->json(['success' => $success], $this->successStatus);
        } else {
            return response()->json(['error' => 'Login credentials do not match!'], $this->failedStatus);
        }
    }
}
