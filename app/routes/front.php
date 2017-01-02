<?php
/**
 * -----------------------------------
 * File  : routes.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */
Route::get('/',['as' => 'home','uses' => 'HomeController@index']);
Route::get('/view',['middleware' => 'MyAuth:ddd,ddd','as' => 'view','uses' => 'HomeController@view']);
