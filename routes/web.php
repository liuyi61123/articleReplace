<?php
// Auth::routes();
Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('/login', 'Auth\LoginController@login');
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

Route::get('/', 'Article\ArticlesController@index')->name('home');
Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
Route::get('/api/test', 'Article\ApiController@test')->name('article.api.test');

//文章生成相关路由
Route::group([
    'prefix'    => 'article',
    'namespace' => 'Article',
],function(){
    Route::get('/api/citys/{pid}', 'ApiController@citys')->name('article.api.citys');
    Route::get('/api/cars', 'ApiController@cars')->name('article.api.cars');
    Route::post('/api/params', 'ApiController@params')->name('article.api.params');
    Route::post('/api/paramids', 'ApiController@paramids')->name('article.api.paramids');
    Route::post('/api/paragraphs', 'ApiController@paragraphs')->name('article.api.paragraphs');

    Route::get('/articles/export/{id}', 'ArticlesController@exportZip')->name('article.articles.exportzip');
    Route::post('/articles/export/{id}', 'ArticlesController@export')->name('article.articles.export');
    Route::post('templates/upload_image', 'TemplatesController@upload_image')->name('article.template.upload_image');//上传图片
    Route::delete('templates/delete_image', 'TemplatesController@delete_image')->name('article.template.delete_image');//删除图片
    Route::resource('articles', 'ArticlesController');//文章
    Route::resource('templates', 'TemplatesController');//模板
    Route::resource('images', 'ImagesController');//图片
    Route::resource('params', 'ParamsController');//自定义参数
    Route::resource('paragraphs', 'ParagraphsController');//段落
});


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
