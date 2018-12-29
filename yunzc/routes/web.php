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
//Route::get('/', function () {
//    return view('welcome');
//});
Route::get('/', function () {
    return redirect('/admin');
});
Route::any('/google',function (){
    return view('google');
});
Route::any('/disclaimer','DisclaimerController@index');  //免责声明
Route::any('/demo','DisclaimerController@demo');
Auth::routes();
Route::get('/forums_detail/{id}','IndexController@forums_detail');  //论坛分享页
Route::get('/tiles_detail/{id}','IndexController@tiles_detail');   //瓷砖分享页
Route::any('/xiazai','IndexController@app_down');   //瓷砖分享页
Route::post('/tiles_share','IndexController@tiles_share');   //瓷砖分享获取
Route::post('/forums_share','IndexController@forums_share');   //论坛分享获取
Route::get('/Img/{img}/{w?}/{h?}','IndexController@resizeImg');  //压缩图片
//Route::get('/home', 'HomeController@index')->name('home');

Route::any('/article','ArticleController@index');
Route::any('/test','RedisTestController@test');