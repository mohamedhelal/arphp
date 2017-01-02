<?php
/**
 * -----------------------------------
 * File  : RouteServices.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Services;


abstract class RouteServices extends Services
{

    /**
     * booted all after register
     */
    public final function afterRegister(){
        if(method_exists($this,'boot')){
            $this->app->call([$this,'boot']);
        }
    }
}