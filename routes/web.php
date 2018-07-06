<?php

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::get('/test', 'HomeController@test')->name('test');
Route::get('/test1', 'HomeController@test1')->name('test1');

Route::get('/articles/export/{id}', 'ArticlesController@export')->name('articles.export');
Route::get('/articles/citys/{pid}', 'ArticlesController@citys')->name('articles.citys');
Route::get('/articles/cars/{pid}', 'ArticlesController@cars')->name('articles.cars');
Route::resource('articles', 'ArticlesController');//文章
Route::resource('templates', 'TemplatesController');//模板
Route::resource('params', 'ParamsController');//参数
