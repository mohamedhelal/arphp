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

    protected $namespace = null;
    /**
     * register settings
     * @return mixed
     */
    public function register()
    {
        // TODO: Implement register() method.
    }
    /**
     * booted all after register
     */
    public final function afterRegister(){
        if(method_exists($this,'boot')){
            $this->app->call([$this,'boot']);
        }
    }
}