<?php
/**
 * -----------------------------------
 * File  : app.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */
define('ENVIRONMENT','production');
require_once __DIR__.'/../vendor/AutoLoading.php';
return AutoLoading::get(__DIR__.DS.'..'.DS);