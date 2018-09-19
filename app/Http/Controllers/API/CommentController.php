<?php

namespace App\Http\Controllers\API;

use App\Model\Comment;
use App\Model\Post;
use App\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{

    public function comments($id) {
        $comments = Comment::with('user')->where('post_id', $id)->latest()->get();
        if($comments) {
            return $comments;
        }
        return null;
    }

    public function postComment(Request $request) {
        if(!empty(trim($request->comment))) {
            $post = Post::find($request->get('post_id'));
            $data = [
                'content' => $request->get('comment'),
                'user_id' => Auth::id(),
                'post_id' => $request->get('post_id')
            ];
            Notification::create([
                'user_id' => $post->user_id,
                'content' => Auth::user()->name . ' commented on your post.'
            ]);
            $comment = Comment::create($data);
            $id = $comment->id;
            $newComment = Comment::with('user')->find($id);
            return response()->json($newComment, 201);
        }
        return response()->json(null, 500);
    }

}
