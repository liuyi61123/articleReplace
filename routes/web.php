<?php

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::get('/test', 'HomeController@test')->name('test');

Route::get('/articles/export/{id}', 'ArticlesController@export')->name('articles.export');
Route::resource('articles', 'ArticlesController');
