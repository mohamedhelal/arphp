<?php
/**
 * -----------------------------------
 * File  : constants.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */
define('DS',DIRECTORY_SEPARATOR);
define('EXT','.php');
define('E_INI','.ini');
define('E_XML','.xml');
define('__USER_METHOD__','__USER_METHOD__');
define('__QUERY_STRING__','router');
define('_OLD_INPUT_DATA','_OLD_INPUT_DATA');
/**
 * check if start with
 * @param $haystack
 * @param $needle
 * @return bool
 */
function startsWith($haystack, $needle) {
    if(is_array($needle)){
        foreach ($needle as $item) {
            if(startsWith($haystack,$item)){
                return true;
            }
        }
        return false;
    }
    return  (substr($haystack, 0,strlen($needle)) === $needle);
}

/**
 * check if start with
 * @param $haystack
 * @param $needle
 * @return bool
 */
function endsWith($haystack, $needle) {
    if(is_array($needle)){
        foreach ($needle as $item) {
            if(endsWith($haystack,$item)){
                return true;
            }
        }
        return false;
    }
    return  (substr($haystack, (strlen($haystack) - strlen($needle))) === $needle);
}

/**
 * get class name
 * @param $class
 * @return string
 */
function class_name($class){
    return basename(str_replace(['\\','/',DS],DS,$class));
}