<?php

Auth::routes();

Route::get('/', 'ArticlesController@index')->name('home');
Route::get('/test', 'HomeController@test')->name('test');
Route::get('/test1', 'HomeController@test1')->name('test1');
Route::get('/test2', 'HomeController@test2')->name('test2');
Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

Route::get('/articles/export/{id}', 'ArticlesController@export')->name('articles.export');
Route::get('/articles/citys/{pid}', 'ArticlesController@citys')->name('articles.citys');
Route::get('/articles/cars', 'ArticlesController@cars')->name('articles.cars');
Route::resource('articles', 'ArticlesController');//文章
Route::post('templates/upload_image', 'TemplatesController@upload_image')->name('template.upload_image');//上传图片
Route::delete('templates/delete_image', 'TemplatesController@delete_image')->name('template.delete_image');//删除图片
Route::resource('templates', 'TemplatesController');//模板
Route::resource('params', 'ParamsController');//参数
Route::resource('images', 'ImagesController');//图片

Route::get('/original', 'OriginalController@index')->name('original.index');//伪原创
Route::post('/original', 'OriginalController@store')->name('original.store');//伪原创
