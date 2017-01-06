<?php
/**
 * -----------------------------------
 * File  : Router.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Routing;

use ArPHP\Exceptions\UndefinedExceptions;
use ArPHP\Http\HttpRouteException;
use ArPHP\Http\Request;
use Closure;

/**
 * Class Router
 * @package ArPHP\Routing
 */
class Router
{
    /**
     * delimiter for url
     */
    const DELIMITER = '/';
    /**
     * all routes
     * @var array
     */
    protected $_routes = [];
    /**
     * all methods
     * @var array
     */
    protected static $_methods = ['POST', 'GET', 'HEAD', 'PUT', 'INSERT', 'UPDATE', 'DELETE', 'SELECT'];
    /**
     * all default patterns
     * @var array
     */
    protected $patterns = [
        'ID' => '[0-9]+',
        'TITLE' => '[\w\.]+',
        'STR' => '[\w]+',
        'INT' => '[0-9]+',
        'ANY' => '.+',
        'DAY' => '[0-9]{4}\/[0-9]{1,2}\/[0-9]{1,2}',
        'MONTH' => '[0-9]{4}\/[0-9]{1,2}',
        'SLUG' => '[^\/]+'
    ];
    /**
     * route groups
     * @var array
     */
    protected $groups = [];
    /**
     * @var Route|false
     */
    protected $current;
    /**
     * cache route by names
     * @var array
     */
    protected $names = [];
    /**
     * cache route by action
     * @var array
     */
    protected $actions = [];
    /**
     * @var array
     */
    protected $prepare = [];
    /**
     * set middleware when route
     * @var array
     */
    protected $when = [];

    /**
     * All of the short-hand keys for middleware.
     *
     * @var array
     */
    protected $middleware = [];
    /**
     * @var ResourceRegistrar
     */
    protected $ResourceRegistrar;

    /**
     * Router constructor.
     */
    public function __construct()
    {
        $this->HttpMiddleware(\App\Http\HttpMiddleware::class);
    }

    /**
     * destruct object
     */
    public function __destruct()
    {
        $this->_routes = [];
        $this->actions = [];
        $this->names = [];
    }

    /**
     * set middleware to routes by action
     * @param $url
     * @param array $middleware
     * @param array $_method
     * @return $this
     */
    public function when($url, $middleware = [], $_method = [])
    {
        if (is_array($url)) {
            foreach ($url as $item) {
                $this->when($item, $middleware, $_method);
            }
        } else {
            $_method = (count($_method) == 0 ? array_diff(static::$_methods, ['GET']) : $_method);
            $url = str_replace('*', '.*', $url);
            foreach ($_method as $item) {
                $item = strtoupper($item);
                $this->when[$item][$url] = $middleware;
            }
        }
        return $this;
    }

    /**
     * set HttpMiddleware
     * @param $middleware
     * @return $this
     */
    public function HttpMiddleware($middleware)
    {
        if (is_array($middleware)) {
            foreach ($middleware as $item) {
                $this->HttpMiddleware($item);
            }
        } else {
            $middleware = new $middleware($this);
            if ($middleware instanceof HttpMiddleware) {
                $this->middleware = array_merge($this->middleware, $middleware->getMiddleware());
            }
        }
        return $this;
    }

    /**
     * @return int
     */
    private function prepareCount()
    {
        $count = count($this->prepare);
        return ($count == 0 ? 0 : $count - 1);
    }

    /**
     * add name
     * @param $name
     * @return $this
     */
    public function name($name)
    {
        $count = $this->prepareCount();
        $this->prepare[$count]['as'] = $name;
        return $this;
    }

    /**
     * add prefix
     * @param $name
     * @return $this
     */
    public function prefix($name)
    {
        $count = $this->prepareCount();
        $this->prepare[$count]['prefix'] = $name;
        return $this;
    }

    /**
     * add middleware
     * @param $name
     * @return $this
     */
    public function middleware($name)
    {
        $count = $this->prepareCount();
        $this->prepare[$count]['middleware'] = $name;
        return $this;
    }

    /**
     * @return false|Route
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * set patterns
     * @param $name
     * @param $pattern
     * @return $this
     */
    public function pattern($name, $pattern = null)
    {
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                $this->pattern($key, $value);
            }
        } else {
            $this->patterns[trim($name, ' ?')] = trim($pattern, ')( ');
        }
        return $this;
    }

    /**
     * get all patterns
     * @return array
     */
    public function getPatterns()
    {
        return $this->patterns;
    }

    /**
     * get router url for matches
     * @return \ArPHP\Http\Access|mixed|string
     */
    public function getRouterUrl()
    {
        if (config('app.queryString')) {
            return request()->queryString();
        } else {
            return request()->path();
        }
    }

    /**
     * @param array $attributes
     */
    protected function mergeAttributes(array $attributes)
    {
        $group = (count($this->groups) ? end($this->groups) : []);
        foreach ($attributes as $key => $val) {
            $key = strtolower($key);
            $group_value = null;
            if (isset($group[$key])) {
                $group_value = $group[$key];
            }
            if ($key === 'namespace') {
                $val = (!empty($group_value) ? rtrim($group_value, '\\') . '\\' : null) . $val;
                if (!empty($val)) {
                    $val = rtrim($val, '\\') . '\\';
                }
            } elseif ($key === 'prefix') {
                $val = static::DELIMITER . trim((!empty($group_value) ? rtrim($group_value, static::DELIMITER) . static::DELIMITER : null) . $val, ' ' . static::DELIMITER);
            } elseif ($key === 'https') {
                $val = ($val === true);
            } elseif ($key === 'as') {
                $val = $group_value . $val;
            } elseif ($key === 'middleware' && !empty($val)) {
                if (!empty($group_value) && !is_array($group_value)) {
                    $group_value = [$group_value];
                }
                $group_value = array_merge((array)$group_value, (array)$val);
                $val = $group_value;
            }
            $group[$key] = $val;
        }
        $this->groups[] = $group;
    }

    /**
     * @param $attributes
     * @param null $closure
     */
    public function group($attributes, $closure = null)
    {
        $prepare = array_pop($this->prepare);
        if ($attributes instanceof Closure) {
            $closure = $attributes;
            $attributes = (array)$prepare;
        } else {
            $attributes = array_merge((array)$prepare, $attributes);
        }
        $prepare = null;
        $this->mergeAttributes($attributes);
        call_user_func($closure, $this);
        array_pop($this->groups);
    }

    /**
     * set method routes
     * @param $methods
     * @param $uri
     * @param $action
     * @return Route|RoutesCollection
     */
    public function match($methods, $uri, $action)
    {
        $prepare = array_pop($this->prepare);
        if ($action instanceof Closure || is_string($action)) {
            $action = array_merge((array)$prepare, ['uses' => $action]);
        } elseif (is_array($action)) {
            $action = array_merge((array)$prepare, $action);
        }
        $prepare = null;
        if ($methods == '*') {
            return $this->match(static::$_methods, $uri, $action);
        } elseif (is_array($uri)) {
            $routes = new RoutesCollection();
            foreach ($uri as $item) {
                $routes->addRoute($this->match($methods, $item, $action));
            }
            return $routes;
        }
        $methods = array_map('strtoupper', (array)$methods);
        $route = new Route($uri, $action, $methods, end($this->groups), $this->when);
        $this->_routes[] = $route;
        return $this->_routes[count($this->_routes) - 1];
    }


    /**
     * display router
     */
    public function dispatch()
    {
        $this->current = $this->getUrlRoute();
        $args = [$this->middleware,$this->current];
        app()->instance(Route::class, $this->current);
        app()->singleton(HttpProtection::class,function () use (&$args){
            return new HttpProtection($args[0],$args[1]);
        });
        $HttpProtection = app(HttpProtection::class, []);
        $controller = $this->current->getController();
        $response = null;
        ob_start();
        if ($controller instanceof Closure) {
            $response = $HttpProtection->handler(function (Request $request) use ($controller) {
                return app()->call($controller, $request->route()->getSegments());
            });
        } else {
            if (!class_exists($controller)) {
                throw new HttpException();
            }
            $controller = app()->make($controller);
            $response = $HttpProtection->handler(function (Request $request) use ($controller) {
                return $controller->__callControllerMethod($request);
            },$this->current->getMethod());
        }
        if (is_null($response)) {
            $response = ob_get_clean();
        }
        response()->setContent($response);
        $controller = null;
        $HttpProtection = null;
        $response = null;
        $request = null;
    }


    /**
     * @return mixed
     * @throws HttpException
     */
    private function getUrlRoute()
    {
        $method = request()->method();
        $routes = $this->_routes;
        foreach ($routes as $route) {
            if ($route->checkMethod($method) && $route->validate()) {
                return $route;
            }
        }
        throw new HttpException();
    }

    /**
     * get route by name
     * @param $name
     * @return mixed
     */
    public function findRouteByName($name)
    {
        if (isset($this->names[$name])) {
            return $this->names[$name];
        }
        foreach ($this->_routes as $route) {
            if ($route->getName() == $name) {
                return $this->names[$name] = $route;
            }
        }
        return false;
    }

    /**
     * get route by action
     * @param $action
     * @return mixed
     * @throws HttpRouteException
     */
    public function findRouteByAction($action)
    {
        if (isset($this->actions[$action])) {
            return $this->actions[$action];
        }
        foreach ($this->_routes as $route) {
            if ($route->getAction() == $action) {
                return $this->actions[$action] = $route;
            }
        }
        throw new HttpRouteException('Undefined Route Action "' . $action . '"');
    }

    /**
     * add action to all  method
     * @param $uri
     * @param $action
     * @return Route|RoutesCollection
     */
    public function all($uri, $action)
    {
        return $this->match(static::$_methods, $uri, $action);
    }

    /**
     * add action to post  method
     * @param $uri
     * @param $action
     * @return Route|RoutesCollection
     */
    public function post($uri, $action)
    {
        return $this->match('POST', $uri, $action);
    }

    /**
     * add action to get  method
     * @param $uri
     * @param $action
     * @return Route|RoutesCollection
     */
    public function get($uri, $action)
    {
        return $this->match('GET', $uri, $action);
    }

    /**
     * add action to head  method
     * @param $uri
     * @param $action
     * @return Route|RoutesCollection
     */
    public function head($uri, $action)
    {
        return $this->match('HEAD', $uri, $action);
    }

    /**
     * add action to put  method
     * @param $uri
     * @param $action
     * @return Route|RoutesCollection
     */
    public function put($uri, $action)
    {
        return $this->match('PUT', $uri, $action);
    }

    /**
     * add action to insert  method
     * @param $uri
     * @param $action
     * @return Route|RoutesCollection
     */
    public function insert($uri, $action)
    {
        return $this->match('INSERT', $uri, $action);
    }

    /**
     * add action to update  method
     * @param $uri
     * @param $action
     * @return Route|RoutesCollection
     */
    public function update($uri, $action)
    {
        return $this->match('UPDATE', $uri, $action);
    }

    /**
     * add action to delete  method
     * @param $uri
     * @param $action
     * @return Route|RoutesCollection
     */
    public function delete($uri, $action)
    {
        return $this->match('DELETE', $uri, $action);
    }

    /**
     * add action to select  method
     * @param $uri
     * @param $action
     * @return Route|RoutesCollection
     */
    public function select($uri, $action)
    {
        return $this->match('SELECT', $uri, $action);
    }

    /**
     * @param $uri
     * @param $action
     */
    public function resource($uri, $action)
    {
        if (!($this->ResourceRegistrar instanceof ResourceRegistrar)) {
            $this->ResourceRegistrar = app(ResourceRegistrar::class, [$this]);
        }
        $this->ResourceRegistrar->registrar($uri, $action);
    }


}