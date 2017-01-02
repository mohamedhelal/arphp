<?php
/**
 *#--------------------------------
 * Project name phpframe
 *#--------------------------------
 * Created by mohamed.
 * File name  Cookie.php
 * Date       20/07/15
 */

namespace ArPHP\Cookie;
use ArPHP\Encryption\Crypter;
use Arr;
abstract class Cookie
{
    /**
     * set cookies
     * @param $name
     * @param $value
     * @param int $expire
     * @param string $path
     * @param bool|false $domain
     * @param bool|false $secure
     * @param bool|false $httponly
     */
    public static function set($name,$value,$expire = 0 ,$path ='/',$domain = false,$secure = false,$httponly = false){
        static::destroy($name,$path ,$domain ,$secure ,$httponly );
        $value = Crypter::encrypt(json_encode($value));
        setcookie($name,$value,$expire,$path,$domain,$secure,$httponly);
    }

    /**
     * get cookie
     * @param $name
     * @return bool
     */
    public static function get($name){
        $value = Arr::get($_COOKIE,$name,false);
        return ($value != false ? json_decode(Crypter::decrypt($value)) : false);
    }

    /**
     * destroy cookie
     * @param $name
     * @param string $path
     * @param bool|false $domain
     * @param bool|false $secure
     * @param bool|false $httponly
     */
    public static function destroy($name,$path ='/',$domain = false,$secure = false,$httponly = false){
        setcookie($name,null,(time() - (time() * 8)),$path,$domain,$secure,$httponly);
    }
}