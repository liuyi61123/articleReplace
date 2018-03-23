<?php

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::get('/test', 'HomeController@test')->name('test');

Route::resource('articles', 'ArticlesController');
