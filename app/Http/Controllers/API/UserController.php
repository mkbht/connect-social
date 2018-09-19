<?php

namespace App\Http\Controllers\API;

use App\Model\Post;
use App\Notification;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function user($id=null) {
        if($id == null) {
            $id = Auth::id();
        }
        $user = User::find($id);
        return $user;
    }

    public function profile($username=null) {
        if($username == null) {
            $username = Auth::user()->username;
        }
        $user = User::where('username', $username)->first();
        return $user;
    }

    public function random() {
        $users = User::where('id', '!=', Auth::id())->random()->get();
        return $users;
    }


    public function follow(Request $request) {
        $user = User::find(Auth::id());
        $user->follow($request->id);
        Notification::create([
            'user_id' => $request->id,
            'content' => Auth::user()->name . ' started following you.'
        ]);
        return response()->json([
            'success'=> 'true'
        ]);
    }

    public function unfollow(Request $request) {
        $user = User::find(Auth::id());
        $user->unfollow($request->id);
        return response()->json([
            'success'=> 'true'
        ]);
    }

    public function posts($id) {
        $posts = Post::where('user_id', $id)->paginate(20);
        return $posts;
    }
}
