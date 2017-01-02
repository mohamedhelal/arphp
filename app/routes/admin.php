<?php
/**
 * -----------------------------------
 * File  : routes.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */
Route::get('/{id}/{name?}',['as' => 'home','uses' => 'HomeController@index']);
Route::resource('home',['as' => 'rehome','uses' => 'HomeController@index']);
