<?php

namespace App;

use App\Model\Post;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;
use Overtrue\LaravelFollow\FollowTrait;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, FollowTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'posts'
    ];

    protected $appends = [
        'is_following', 'total_followers', 'total_followings', 'total_posts',
        'notifications_count', 'new_notifications_count'
    ];

    public function posts() {
        return $this->hasMany('App\Model\Post', 'user_id');
    }

    public function getIsFollowingAttribute() {
        $user = User::find(Auth::id());
        return $user->isFollowing($this->id);
    }

    public function getTotalFollowersAttribute() {
        return $this->followers()->count();
    }

    public function getTotalFollowingsAttribute() {
        return $this->followings()->count();
    }

    public function getTotalPostsAttribute() {
        return $this->posts->count();
    }

    public function notifications() {
        return $this->hasMany('App\Notification', 'user_id');
    }

    public function getNotificationsCountAttribute() {
        $notifications = Notification::where('user_id', Auth::id())->count();
        return $notifications;
    }

    public function getNewNotificationsCountAttribute() {
        $notifications = Notification::where('user_id', Auth::id())->where('seen', 0)->count();
        return $notifications;
    }
}
