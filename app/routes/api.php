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