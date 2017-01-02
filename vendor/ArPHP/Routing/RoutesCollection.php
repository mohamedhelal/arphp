<?php
/**
 * -----------------------------------
 * File  : RoutesCollection.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Routing;
use ArPHP\Exceptions\UndefinedExceptions;

/**
 * Class RoutesCollection
 * @package ArPHP\Routing
 */
class RoutesCollection
{
    protected $_routes = [];

    /**
     * @param Route $route
     */
    public function addRoute(Route $route){
        $this->_routes[] = $route;
    }

    /**
     * @param $name
     * @param $arguments
     * @throws UndefinedExceptions
     */
    public function __call($name, $arguments)
    {
        $loaded = false;
        foreach ($this->_routes as $route) {
            if(method_exists($route,$name)){
                $loaded = true;
                call_user_func_array([$route,$name],$arguments);
            }
        }
        if(!$loaded){
            throw new UndefinedExceptions(' Call to undefined method  ' . Route::class . '::' . $name . '()');
        }
    }
}