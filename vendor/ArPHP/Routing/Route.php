<?php
/**
 * -----------------------------------
 * File  : Route.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Routing;


use ArPHP\Exceptions\UndefinedExceptions;
use ArPHP\Support\Arr;

class Route
{
    protected
        /**
         * default user method for call
         * @var string
         */
        $_call_method,
        /**
         * default action
         * @var array|mixed
         */
        $action,
        /**
         * route name
         * @var string
         */
        $name,
        /**
         * route namespace
         * @var string
         */
        $namespace,
        /**
         * controller class
         * @var string
         */
        $controller,
        /**
         * controller method
         * @var string
         */
        $method,
        /**
         * domain name
         * @var bool|mixed
         */
        $domain = false,
        /**
         * uri string
         * @var string
         */
        $uri,
        /**
         * check if is https
         * @var bool
         */
        $https = false,
        /**
         * url args
         * @var array
         */
        $segment = [],
        /**
         * route middleware
         * @var array
         */
        $middleware = [],
        /**
         * @var array
         */
        $pattern = [];

    public function __construct($uri, $action, $type, $group, $when = [])
    {

        $this->_call_method = $type;
        $this->action = $action;
        $this->setUriPrefix($uri, $group,$when);
        $this->setActionDetails($action);
    }

    /**
     * get real url
     * @return string
     */
    public function getRealUrl(){
        return $this->domain.$this->uri;
    }

    /**
     * check if route method exists
     * @param $method
     * @return bool
     */
    public function checkMethod($method){
        return (in_array(strtoupper($method),$this->_call_method));
    }
    /**
     * add route name
     * @param $name
     * @return $this
     */
    public function name($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * add route middleware
     * @param $middleware
     * @return $this
     */
    public function middleware($middleware)
    {
        $this->middleware = array_merge($this->middleware, (array)$middleware);
        return $this;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws UndefinedExceptions
     */
    public static function __callStatic($name, $arguments)
    {
        $router = app(Router::class);
        if (method_exists($router, $name)) {
            return call_user_func_array([$router, $name], $arguments);
        }
        throw new UndefinedExceptions(' Call to undefined method  ' . get_class($router) . '::' . $name . '()');
    }

    /**
     * set patterns
     * @param $name
     * @param $pattern
     * @return $this
     */
    public function where($name, $pattern = false)
    {
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                $this->where($key, $value);
            }
        } else {
            $this->pattern[trim($name, ' ?')] = trim($pattern, ')( ');
        }
        return $this;
    }
    /**
     * @return mixed
     */
    public function getSegments()
    {
        return $this->segment;
    }
    /**
     * @param $index
     * @param bool $default
     * @return bool
     */
    public function segment($index, $default = false)
    {
        return Arr::get($this->segment, strtolower($index), $default);
    }


    /**
     * @return bool
     */
    public function getDomain()
    {
        return $this->domain;
    }
    /**
     * get route name
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return mixed
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return mixed
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @return bool
     */
    public function isHttps()
    {
        return $this->https;
    }

    /**
     * @return array
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }
    /**
     * set prefix to uri
     * @param $uri
     * @param $group
     * @param array $when
     */
    protected function setUriPrefix($uri, $group, $when = [])
    {
        settype($group, 'array');
        foreach ($group as $key => $value) {
            if ($key === 'prefix' && !empty($value)) {
                $this->uri = $value;
            } elseif ($key === 'namespace') {
                $this->namespace = $value;
            } elseif ($key === 'domain') {
                $this->domain = $value;
            } elseif ($key === 'https') {
                $this->https = $value;
            } elseif ($key === 'as') {
                $this->name = $value;
            } elseif ($key === 'middleware') {
                $this->middleware = array_merge($this->middleware, (array)$value);
            }
        }
        $this->uri = rtrim($this->uri . Router::DELIMITER . ltrim($uri, Router::DELIMITER), Router::DELIMITER);
        $whenRoute = [];
        foreach ($this->_call_method as $method) {
            if(isset($when[$method])){
                $whenRoute = $when[$method];
                break;
            }
       }
        if (count($whenRoute)) {
            foreach ($whenRoute as $prefix => $middleware) {
                $prefix = ltrim($prefix, '/');
                if (preg_match("#^{$prefix}$#i", ltrim($this->uri, '/'))) {
                    $this->middleware = array_merge($this->middleware, (array)$middleware);
                    break;
                }
            }
        }
    }

    /**
     * set action details
     * @param $action
     */
    protected function setActionDetails($action)
    {
        if (is_string($action)) {
            $action = ['uses' => $action];
        }
        if (!is_array($action)) {
            $action = [$action];
        }

        foreach ($action as $key => $value) {
            if ($key === 'namespace') {
                $this->namespace = (!empty($this->namespace) ? rtrim($this->namespace, '\\') . '\\' : null) . ltrim($value, '\\');
            } elseif ($key === 'as') {
                $this->name = $this->name . $value;
            } elseif ($key === 'uses' || $value instanceof \Closure) {
                $this->action = $value;
            } elseif ($key === 'middleware') {
                $this->middleware = array_merge($this->middleware, (array)$value);
            }
        }
        if (count($this->middleware)) {
            $this->middleware = array_unique($this->middleware);
        }
    }

    /**
     * replace Patterns
     * @param $url
     * @return mixed
     */
    protected function replace($url)
    {
        $wheres = $this->pattern;
        $patterns = static::getPatterns();
        $url = preg_replace_callback('#(?P<ds>/?){(?P<name>[\w:]+)(?:\s+as\s+(?P<as>[\w:]+))?(?P<end>\??)}#ui', function ($match) use ($wheres, $patterns) {
            $name = $match['name'];
            if (isset($wheres[$name])) {
                $value = $wheres[$name];
            } elseif (isset($patterns[$name])) {
                $value = $patterns[$name];
            } else {
                $upper = strtoupper($name);
                if (isset($patterns[$upper])) {
                    $value = $patterns[$upper];
                } else {
                    $value = $patterns['STR'];
                }
            }
            $name = strtolower($name);
            $value = (empty($match['as']) ? $value : (isset($patterns[strtoupper($match['as'])]) ? $patterns[strtoupper($match['as'])] : $value));
            $name  = str_replace(':',null,$name);
            return '(?:' . $match['ds'] . '(?P<' . $name . '>' . $value . '))' . $match['end'];
        }, $url);
        return $url;
    }

    /**
     * check domain name and https
     * @return bool
     */
    protected function domain()
    {
        if ($this->https == true && !request()->getHttps()) {
            return false;
        }
        if (!$this->domain) {
            return true;
        }
        $domain = request()->getDomain();
        if($this->domain != false && $domain == $this->domain){
            return true;
        }
        if (preg_match('#^' . $this->replace($this->domain) . '$#u', $domain, $match)) {
            $this->segment = array_slice($match, 1);
            return true;
        }
        return false;
    }


    /**
     * validate route
     * @return mixed
     */
    public function validate()
    {
        if (!$this->domain()) {
            return false;
        }
        $queryString = static::getRouterUrl();
        $url = trim($queryString, Router::DELIMITER);
        if ($url == trim($this->uri, Router::DELIMITER)) {
            $this->setRouteControllerDetails();
            return true;
        }
        $url = trim($queryString,Router::DELIMITER);
        $replace = $this->replace(trim($this->uri,Router::DELIMITER));
        if (preg_match('#^' . $replace . '$#u', $url, $match)) {
            $this->segment = array_merge($this->segment, array_slice($match, 1));
            $this->setRouteControllerDetails();
            return true;
        }
        return false;
    }

    /**
     * set route controller details
     */
    protected function setRouteControllerDetails(){
        if ($this->action instanceof \Closure) {
            $this->controller = $this->action;
        } elseif (strpos($this->action, '@') != false) {
            $array = explode('@', $this->action, 2);
            $this->controller = trim($array[0]);
            $this->method = (empty($array[1]) ? $this->method : $array[1]);
        }
        if (is_string($this->controller)) {
            $this->namespace = trim($this->namespace);
            if(!empty($this->namespace) && startsWith($this->controller,$this->namespace)){
                $this->controller = substr($this->controller,strlen($this->namespace));
            }
            $this->controller = $this->namespace.$this->controller;
        }
    }

}