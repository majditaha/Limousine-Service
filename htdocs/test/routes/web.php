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
// Route::get('/', function () {
//     return view('emails/sample');
// });

Route::get('/login/vkontakte', 'Auth\Social\VkontakteController@redirectToProvider');
Route::get('/login/vkontakte/callback', 'Auth\Social\VkontakteController@handleProviderCallback');

Route::group(['prefix' => 'auth'], function() {
    Route::post('/login', 'Auth\LoginController@login');
    Route::post('/register', 'Auth\RegisterController@register');
    Route::post('/password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('/password/reset', 'Auth\ResetPasswordController@reset');
    Route::get('/confirm_email/{token}', 'Auth\RegisterController@confirmEmail');
    Route::get('/check', 'Auth\LoginController@check');
    Route::get('/logout', 'Auth\LoginController@logout');
});

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin']], function() {
    Route::get('/', 'HomeController@admin');
    Route::get('/{any}', 'HomeController@admin')->where('any', '(?!images).*');
});

$condition = '(?!images).*';
Route::any('/{any}', 'HomeController@site')->where('any', $condition)->middleware('auth');
Route::any('/{any}', 'HomeController@site')->where('any', $condition);

Auth::routes();