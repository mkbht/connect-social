<?php

namespace App\Http\Controllers\API;

use App\Http\Sentiment\Analyzer;
use App\Http\Sentiment\Sentiment;
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
        sleep(1);
        $posts = Post::with('user')->latest()->simplePaginate(20);
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

    public function sentiment($id) {
        //sleep(2);
        $post = Post::find($id);
        //dd($post);
        if($post) {
            $sentiment = new Analyzer(new Sentiment());

            $sentiment->train(asset('sentimentData/pos.txt'), 'pos', 5000);
            $sentiment->train(asset('sentimentData/neg.txt'), 'neg', 5000);

            $score = $sentiment->score($post->content);
            return $score;
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
        $post = Post::find($id);
        if($post) {
            if(Auth::id() == $post->user_id) {
                $post->delete();
            }
        }
    }
}
