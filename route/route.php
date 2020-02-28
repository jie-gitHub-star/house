<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});


Route::get('hello/:name', 'index/hello');
Route::resource('login','Admin/Login');


/* 后台 */
Route::group('', function(){ /* 检测登录  这里的控制器的访问方法为   */

	Route::get('admin','Admin/Index/index');
	Route::resource('house','Admin/house');
	Route::get('addhouse','Admin/House/addhouse');

})->middleware('Check');

/*  小程序接口 */

Route::resource('v1/houses','Admin/Api');
Route::get('v1/cates','Admin/Api/cates');

Route::get('v1/searchs','Admin/Api/searchs');
Route::get('v1/openId','Admin/Api/opendId');
Route::get('v1/descsearch','Admin/Api/descsearch');
Route::get('v1/getcolls','Admin/Api/getcoll');



return [

];


