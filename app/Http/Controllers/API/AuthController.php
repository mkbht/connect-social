<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\SignupRequest;
use App\User;
use Hash;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public $successStatus = 200;
    public $failedStatus = 401;

    public function login()
    {
        //sleep(2);
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

    public function signup(Request $request) {
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => $request->password,
            'password_confirmation' => $request->password_confirmation
        ];

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|alpha_dash|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ];
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {

            //pass validator errors as errors object for ajax response

            return response()->json(['errors'=>$validator->errors()], 500);
        }


        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
        ]);
        $token = $user->createToken('connectToken')->accessToken;
        return response()->json([
            'success' => true,
            'token' => $token
        ]);
    }
}
