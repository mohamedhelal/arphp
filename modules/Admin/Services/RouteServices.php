<?php
/**
 * -----------------------------------
 * File  : RouteServices.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace Modules\Admin\Services;


use ArPHP\Routing\Router;

class RouteServices extends \App\Services\RouteServices
{

    protected $namespace = 'Modules\Admin\Http\Controllers';
    /**
     * get admin routes
     * @param Router $router
     */
    protected function adminRoutesFile(Router $router){
            require_once  realpath(__DIR__.DS.'..'.DS.'routes'.DS.'admin.php');
    }

    /**
     * get front page routes
     * @param Router $router
     */
    protected function frontRoutesFile(Router $router){
        require_once  realpath(__DIR__.DS.'..'.DS.'routes'.DS.'front.php');
    }

    /**
     * get api routes
     * @param Router $router
     */
    protected function apiRoutesFile(Router $router){
        require_once realpath(__DIR__ . DS . '..' . DS . 'routes' . DS . 'api.php');
    }
}