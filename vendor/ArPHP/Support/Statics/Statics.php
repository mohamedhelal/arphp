<?php
/**
 * -----------------------------------
 * File  : Statics.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Support\Statics;


abstract class Statics
{
    /**
     * get object name
     * @return mixed
     */
    protected static function getStaticName(){}
    /**
     * get static object
     * @return \ArPHP\Application\Application|mixed
     */
   protected static final function getObjectByName(){
       $app = app();
       $name = static::getStaticName();
       if(isset($app[$name])){
           return $app[$name];
       }
       return app($name);
   }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
   public static final function __callStatic($name, $arguments)
   {
       $object = static::getObjectByName();
       return call_user_func_array([$object,$name],$arguments);
   }
}