<?php

use App\Http\Middleware\ApiAuthMiddleware;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/api/user/auth/register', 'UserController@register')->middleware(ApiAuthMiddleware::class);

Route::post('/api/user/auth/login', 'UserController@login');

Route::put('/api/user/update', 'UserController@update')->middleware(ApiAuthMiddleware::class);

