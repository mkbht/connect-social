<?php

namespace App\Http\Controllers\API;

use App\Model\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{

    public function comments($id) {
        $comments = Comment::with('user')->where('post_id', $id)->get();
        if($comments) {
            return $comments;
        }
        return null;
    }

    public function postComment(Request $request) {
        if(!empty(trim($request->comment))) {
            $comment = new Comment();
            $comment->post_id = $request->id;
            $comment->user_id = Auth::id();
            $comment->content = $request->comment;
            $comment->save();
            return $comment;
        }
        return null;
    }

}
