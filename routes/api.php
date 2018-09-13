<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('/login', 'API\AuthController@login');

Route::middleware('auth:api')->group(function (){
    Route::get("/post/sentiment/{id}", "API\PostController@sentiment");
    Route::resource("/post","API\PostController");
    Route::get("/like/{id}", "API\LikeController@like");
    Route::get("/comments/{id}", "API\CommentController@comments");
    Route::post("/comment", "API\CommentController@postComment");

    // Profile
    Route::get('/user/{id?}', "API\UserController@user");
    Route::get('/user/{id}/posts', "API\UserController@posts");
});

//Route::post('register', 'Api\UserController@register');
//Route::group(['middleware' => 'auth:api'], function () {
//    Route::post('details', 'API\UserController@details');
//});
