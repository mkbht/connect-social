<?php

namespace App\Http\Controllers\API;

use App\Model\Post;
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

    public function posts($id) {
        $posts = Post::where('user_id', $id)->paginate(20);
        return $posts;
    }
}
