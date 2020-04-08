<?php
// Auth::routes();
Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('/login', 'Auth\LoginController@login');
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

Route::get('/', 'ArticlesController@index')->name('home');
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

Route::get('/baidu_urls', 'BaiduUrlsController@index')->name('baidu_urls.index');//百度收录
Route::get('/baidu_urls/create', 'BaiduUrlsController@create')->name('baidu_urls.create');//百度收录
Route::post('/baidu_urls', 'BaiduUrlsController@store')->name('baidu_urls.store');//百度收录
Route::delete('/baidu_urls/{id}', 'BaiduUrlsController@destroy')->name('baidu_urls.destroy');//百度收录
Route::get('/baidu_urls/{id}/search', 'BaiduUrlsController@search')->name('baidu_urls.search');//百度收录

Route::post('websites/upload', 'WebsitesController@upload');//网站管理
Route::post('websites/remove', 'WebsitesController@removeUrls');//网站管理
Route::get('websites/api', 'WebsitesController@api');//网站管理
Route::resource('websites', 'WebsitesController');//网站管理

Route::get('website-categories/api', 'WebsiteCategoriesController@api');//网站api
Route::resource('website-categories', 'WebsiteCategoriesController');//网站分类

Route::get('website-pushes/manual', 'WebsitePushesController@manual')->name('website-pushes.manual');//手动提交
Route::resource('website-pushes', 'WebsitePushesController');
