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

//Route::get('/', function () {
//    return view('welcome');
//});

// 首页
Route::get('/', 'StaticPagesController@home')->name('home');
Route::get('/help', 'StaticPagesController@help')->name('help');
Route::get('/about', 'StaticPagesController@about')->name('about');

// 用户注册
Route::get('signup', 'UsersController@create')->name('signup');
Route::resource('users', 'UsersController');
// 调用 resource 严格按照了 RESTful 架构对路由进行设计。等同于
//Route::get('/users', 'UsersController@index')->name('users.index');
//Route::get('/users/create', 'UsersController@create')->name('users.create');
//Route::get('/users/{user}', 'UsersController@show')->name('users.show');
//Route::post('/users', 'UsersController@store')->name('users.store');
//Route::get('/users/{user}/edit', 'UsersController@edit')->name('users.edit');
//Route::patch('/users/{user}', 'UsersController@update')->name('users.update');
//Route::delete('/users/{user}', 'UsersController@destroy')->name('users.destroy');
// HTTP 请求  	URL	                    动作	                    作用
// GET	    /users	            UsersController@index	    显示所有用户列表的页面
// GET	    /users/{user}	    UsersController@show	    显示用户个人信息的页面
// GET  	/users/create	    UsersController@create	    创建用户的页面
// POST	    /users	            UsersController@store	    创建用户
// GET	    /users/{user}/edit	UsersController@edit	    编辑用户个人资料的页面
// PATCH	/users/{user}	    UsersController@update	    更新用户
// DELETE	/users/{user}	    UsersController@destroy	    删除用户

// 用户登录
Route::get('login', 'SessionsController@create')->name('login');
Route::post('login', 'SessionsController@store')->name('login');
Route::delete('logout', 'SessionsCOntroller@destroy')->name('logout');

// 重置密码
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');    // 显示重置密码的邮箱发送页面
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');      // 邮箱发送重置链接
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');     // 密码更新页面
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');                   // 执行密码更新操作

// 邮箱激活账户
Route::get('signup/confirm/{token}', 'UsersController@confirmEmail')->name('confirm_email');

// 用户个人信息编辑
Route::get('/users/{user}/edit', 'UsersController@edit')->name('users.edit');

// 微故事操作
Route::resource('statuses', 'StatusesController', ['only' => ['store', 'destroy']]);

// 粉丝、关注的人页面
Route::get('/users/{user}/followings', 'UsersController@followings')->name('users.followings'); // 关注的人
Route::get('/users/{user}/followers', 'UsersController@followers')->name('users.followers');    // 粉丝

// 关注、取消关注
Route::post('/users/followers/{users}', 'FollowersController@store')->name('followers.store');
Route::delete('/users/followers/{users}', 'FollowersController@destroy')->name('followers.destroy');

