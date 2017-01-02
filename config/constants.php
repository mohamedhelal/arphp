<?php

/**
 * -----------------------------------
 *  project name framework
 * -----------------------------------
 * File:constants.php
 * User: mohamed
 */
/**
  #--------------------------------------------------------------------
  # ملف الثوابت العامة
  #--------------------------------------------------------------------
 */
/**
  #--------------------------------------------------------------------
  # واخد الثوابت من كود اجنيتر
  #--------------------------------------------------------------------
 */
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
  |--------------------------------------------------------------------------
  | File Stream Modes
  |--------------------------------------------------------------------------
  |
  | These modes are used when working with fopen()/popen()
  |
 */

define('FOPEN_READ', 'rb');
define('FOPEN_READ_WRITE', 'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 'ab');
define('FOPEN_READ_WRITE_CREATE', 'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');



/**
 * modules folder path
 */
define('CMS_MODULES_PATH',base_path().'modules'.DS);