<?php
/**
 * -----------------------------------
 * File  : RouteServices.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace App\Services;


use ArPHP\Routing\Router;

class RouteServices extends \ArPHP\Services\RouteServices
{

    protected $namespace = 'App\Http\Controllers';
    /**
     * register settings
     * @return mixed
     */
    public function register()
    {
        // TODO: Implement register() method.
    }
    /**
     * @param Router $router
     */
    public function boot(Router $router){
        $router->group(['namespace' => $this->namespace],function ()use ($router){
            $this->adminRoutes($router);
            $this->apiRoutes($router);
            $this->frontRoutes($router);
        });
    }

    /**
     * get admin routes
     * @param Router $router
     */
    protected function adminRoutes(Router $router){
        $router->group(['prefix' => 'admin','as' => 'admin.'],function ()use ($router){
            $this->adminRoutesFile($router);
        });
    }

    /**
     * get front page routes
     * @param Router $router
     */
    protected function frontRoutes(Router $router){
       $this->frontRoutesFile($router);
    }

    /**
     * get api routes
     * @param Router $router
     */
    protected function apiRoutes(Router $router){
        $router->group(['prefix' => 'api','as' => 'api.'],function ()use ($router) {
           $this->apiRoutesFile($router);
        });
    }
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