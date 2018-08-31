<?php

namespace App\Http\Controllers\API;

use App\Model\Post;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::with('user')->latest()->simplePaginate(5);
        return response()->json($posts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = [
            'content' => $request->get('content'),
            'user_id' => Auth::id(),
        ];

        $post = Post::create($data);
        $id = $post->id;
        $post = Post::with('user')->find($id);
        return response()->json($post, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::with('user')->find($id);
        if ($post) {
            return response()->json($post);
        } else {
            return response()->json([
                'message' => 'Not Found',
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = [
            'content' => $request->get('content'),
            'user_id' => Auth::id(),
        ];

//        return Post::find($id)->update($data);

        $post = Post::findOrFail($id);
        if ($post) {
            $post->update($data);
            return response()->json($post, 200);
        } else {
            return response()->json([
                'message' => 'Not Found',
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
