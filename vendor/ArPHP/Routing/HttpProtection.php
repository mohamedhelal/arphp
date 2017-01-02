<?php
/**
 * -----------------------------------
 * File  : HttpProtection.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Routing;


use ArPHP\Http\Request;

class HttpProtection
{
    /**
     * All of the short-hand keys for middleware.
     *
     * @var array
     */
    protected $middleware = [];
    protected $routeMiddleware = [];
    /**
     * @var Route
     */
    protected $route;

    /**
     * HttpProtection constructor.
     * @param $middleware
     * @param Route $route
     */
    public function __construct($middleware, Route $route)
    {
        $this->middleware = $middleware;
        $this->route = $route;
        $this->init();

    }

    protected function init(){
        $middleware = $this->route->getMiddleware();
        foreach ($middleware as $item) {
            $this->routeMiddleware[$item] = ['all'];
        }
    }

    /**
     * @param $name
     * @param array $item
     * @return $this
     */
    public function add($name,$item = ['all']){

        $this->routeMiddleware[$name] = $item;
        return $this;
    }

    /**
     * @param $callback
     * @param null $method
     * @return mixed|null|object
     */
    public function handler($callback,$method = null)
    {

        $middleware = $this->routeMiddleware;
        $response = null;
        $_response = function (Request $request) {
            return $request;
        };


        foreach ($middleware as $call => $item) {
            if(!in_array('all',$item) && ($method != null && !in_array($method,$item)) ){
                continue;
            }
            $item = $call;
            $args = [request(), $_response];
            if (strpos($item, ':') !== false) {
                $explode = explode(':', $item, 2);
                $item = $explode[0];
                $args = array_merge($args, explode(',', $explode[1]));
                $explode = null;
            }
            $callable = (isset($this->middleware[$item]) ? $this->middleware[$item] : false);
            if ($callable == false && class_exists($item)) {
                $callable = $item;
            }
            if($callable == false){
                continue;
            }
            if (is_string($callable)) {
                $callable = [$callable, 'handler'];
            }
            $response = app()->call($callable, $args);
            if (!is_null($response) && !is_a($response, Request::class)) {
                return $response;
            }
        }

        return $callback(request());
    }
}