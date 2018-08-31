<?php

namespace App\Http\Controllers\API;

use App\Model\Like;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function like($id)
    {
        $existing_like = Like::withTrashed()->where('post_id', $id)->where('user_id', Auth::id())->first();
        if (is_null($existing_like)) {
            Like::create([
                'post_id' => $id,
                'user_id' => Auth::id()
            ]);
            return 1;
        } else {
            if (is_null($existing_like->deleted_at)) {
                $existing_like->delete();
                return 0;
            } else {
                $existing_like->restore();
                return 1;
            }
        }
    }
}
