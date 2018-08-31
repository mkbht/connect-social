<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    protected $fillable = [
        'user_id', 'content', 'image'
    ];

    protected $appends = [
        'liked', 'like_count', 'comment_count'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function likes()
    {
        return $this->hasMany('App\Model\Like', 'post_id');
    }

    public function comments()
    {
        return $this->hasMany('App\Model\Comment', 'post_id');
    }

    public function getLikeCountAttribute()
    {
        return $this->likes->count();
    }

    public function getCommentCountAttribute()
    {
        return $this->comments->count();
    }

    public function getLikedAttribute()
    {
        $liked = Like::where('user_id', Auth::id())->where('post_id', $this->id)->first();
        if ($liked) {
            return 1;
        }
        return 0;
    }
}
