<?php

use App\Events\WebsocketDemoEvent;

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

Route::get('/', function () {
    // transmite o evento
    broadcast(new WebsocketDemoEvent('some data'));
    return view('welcome');
});

// rota para retorna view
Route::get('/chats', 'ChatsController@index');

// rota para retorna mensagens
Route::get('/messages', 'ChatsController@fetchMessages');
// rota para enviar novas mensagens
Route::post('/messages', 'ChatsController@sendMessage');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
