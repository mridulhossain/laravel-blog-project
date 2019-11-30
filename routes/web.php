<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@index' )->name('home');
Route::get('posts', 'PostController@index' )->name('post.index');
Route::get('post/{slug}','PostController@details')->name('post.details');
Route::get('/category{slug}','PostController@postByCategory')->name('category.post');


Route::post('subscriber','SubscriberController@store')->name('subscriber.store');

Auth::routes();

Route::group(['middleware'=>['auth']],function (){
Route::post('favorite/{post}/add','FavoriteController@add')->name('post.favorite');
Route::post('comment/{post}','CommentController@store')->name('comment.store');
});

//admin Route

Route::group(['as'=>'admin.','prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['auth','admin']],function(){

    Route::get('dashboard','DashboardController@index')->name('dashboard');

    Route::get('setting','SettingController@index')->name('setting');
    Route::put('profile-update','SettingController@updateProfile')->name('profile.update');
    Route::put('password-update','SettingController@updatePassword')->name('password.update');


    Route::resource('tag','TagController');
    Route::resource('category','CategoryController');
    Route::resource('post','PostController');

    Route::get('pending/post','PostController@pending')->name('post.pending');
    Route::put('/post/{id}/approve','PostController@approval')->name('post.approve');
    Route::get('/subscriber','SubscriberController@index')->name('subscriber.index');
    Route::delete('/subscriber/{id}','SubscriberController@destroy')->name('subscriber.destroy');

    //favorite list
    Route::get('/favorite','FavoriteController@index')->name('favorite.index');

  //    comments route
    Route::get('comments','CommentController@index')->name('comment.index');
    Route::delete('comments/{id}','CommentController@destroy')->name('comment.destroy');

});

//author Route

Route::group(['as'=>'author.','prefix' => 'author', 'namespace' => 'Author', 'middleware' => ['auth','author']],function(){
    Route::get('dashboard','DashboardController@index')->name('dashboard');
    Route::resource('post','PostController');

    Route::get('setting','SettingController@index')->name('setting');
    Route::put('profile-update','SettingController@updateProfile')->name('profile.update');
    Route::put('password-update','SettingController@updatePassword')->name('password.update');

    Route::get('/favorite','FavoriteController@index')->name('favorite.index');

    //    comments route
    Route::get('comments','CommentController@index')->name('comment.index');
    Route::delete('comments/{id}','CommentController@destroy')->name('comment.destroy');
});

