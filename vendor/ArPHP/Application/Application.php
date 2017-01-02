<?php
/**
 * -----------------------------------
 * File  : Application.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Application;

/**
 * classes
 */
use ArPHP\Config\Repository;
use ArPHP\Exceptions\Handler;
use ArPHP\Exceptions\HandlerExceptions;
use ArPHP\Exceptions\MyException;
use ArPHP\Http\Request;
use ArPHP\Http\Response;
use ArPHP\Routing\ResourceRegistrar;
use ArPHP\Routing\Router;
use ArPHP\Services\RouteServices;
use ArPHP\Support\Arr;
use Closure, ReflectionClass, ReflectionFunction, ReflectionMethod, ArrayAccess;

/**
 * start flush
 */
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
    ob_start('ob_gzhandler');
} else ob_start();

/**
 * Class Application
 * @package ArPHP\Application
 */
class Application implements ArrayAccess
{
    /**
     * @var \AutoLoading
     */
    protected $loader;
    /**
     * @var string
     */
    protected $_basePath;
    /**
     * default directories for apps
     * @var array
     */
    private static $_directories = [
        'public' => 'public',
        'app' => 'app',
        'config' => 'config',
        'resources' => 'resources',
        'storage' => 'storage',
        'modules' => 'modules',
        'lang' => 'resources' . DS . 'lang',
        'view' => 'resources' . DS . 'view',
        'assets' => 'resources' . DS . 'assets',
    ];
    /**
     * all object instances
     * @var array
     */
    protected static $_instances = [];
    /**
     * binding functions
     * @var array
     */
    protected static $_binding = [];
    /**
     * all directories real path
     * @var array
     */
    protected static $_paths = [];
    /**
     * install Services
     * @var array
     */
    protected static $_services = [];

    /**
     * create new apps
     * @param $_basePath
     * @param \AutoLoading $autoLoading
     * @return Application
     */
    public static function create($_basePath, \AutoLoading &$autoLoading)
    {
        return new self($_basePath, $autoLoading);
    }

    /**
     * get instance from this object
     * @return Application
     */
    public static final function &getInstance()
    {
        return static::$_instances[self::class];
    }

    /**
     * set base path
     * Application constructor.
     * @param array|null|object $basePath
     * @param \AutoLoading $autoLoading
     */
    public final function __construct($basePath, \AutoLoading &$autoLoading)
    {
        $this->loader = $autoLoading;
        $this->share(Handler::class);
        $this->initPath($basePath);
        static::$_instances[self::class] = $this;
    }

    /**
     * set classes aliases
     * @param $aliases
     * @param null $class
     * @return $this
     */
    public function setAlias($aliases, $class = null)
    {
        $this->loader->setAlias($aliases, $class);
        return $this;
    }

    /**
     * set class maps files
     * @param $namespaces
     * @param null $file
     * @return $this
     */
    public function setClassMap($namespaces, $file = null)
    {
        $this->loader->setClassMap($namespaces, $file);
        return $this;
    }

    /**
     * set namespace
     * @param $namespaces
     * @param null $path
     * @return $this
     */
    public function setNamespace($namespaces, $path = null)
    {
        $this->loader->setNamespace($namespaces, $path);
        return $this;
    }

    /**
     * run apps
     */
    public final function run()
    {

        $response = $this->share(Response::class);
        $this->share(Request::class);
        $this->share('config', Repository::class);
        $this->share('Translate', \ArPHP\Translate\Repository::class);
        $this->setAlias(config('app.aliases'));
        $this->setNamespace(config('app.namespaces'));
        $router = &$this->share(Router::class);
        static::$_services = array_merge(static::$_services, config('app.services'));
        $this->registerServices();
        $this->bootServices();
        $router->dispatch();
        $router = null;
        $response = $response->content();
        die($response);
    }

    /**
     * register  Service
     * @param $services
     * @return $this
     */
    public function registerService($services)
    {
       if(is_array($services)){
           foreach ($services as $service) {
               $this->registerService($service);
           }
       }else{
           static::$_services[] = $services;
           $this->share($services)->register();
       }
       return $this;
    }
    /**
     * register all Services
     */
    protected function registerServices()
    {
        foreach (static::$_services as $service) {
            $this->share($service)->register();
        }
    }

    /**
     * boot all Services
     */
    protected function bootServices()
    {
        foreach (static::$_services as $service) {
            $object =& $this->make($service);
            if ($object instanceof RouteServices) {
                $object->afterRegister();
            }
            $object = null;
            $service = null;
        }
    }

    /**
     * init paths for apps
     * @param $basePath
     */
    protected function initPath($basePath)
    {
        $this->_basePath = rtrim($basePath, DS) . DS;
        static::$_paths['basePath'] = $this->_basePath;
        foreach (static::$_directories as $name => $directory) {
            $path = $this->_basePath . $directory;
            if (is_dir($path)) {
                static::$_paths["{$name}"] = $path . DS;
            }
        }
    }

    /**
     * get real path
     * @param $name
     * @param bool $value
     * @return bool|mixed
     */
    public function path($name, $value = false)
    {
        if (array_key_exists($name, static::$_paths)) {
            if ($value != false) {
                static::$_paths[$name] = $value;
            }
            return static::$_paths[$name];
        }
        return false;
    }

    /**
     * singleton  class
     * @param $abstract
     * @param null $content
     * @return mixed
     */
    public function singleton($abstract, $content = null)
    {
        $this->bind($abstract, $content, true);
    }

    /**
     * binding class
     * @param $abstract
     * @param null $content
     * @param bool $singleton
     * @return Closure|null
     */
    public function bind($abstract, $content = null, $singleton = false)
    {
        if ($content == null) {
            $content = $abstract;
        }
        if (!$content instanceof Closure) {
            $content = function ($app, $parameters = []) use ($abstract, $content) {
                if ($abstract === $content) {
                    return $this->getClass($content, $parameters);
                }
                return $app->make($content, $parameters);
            };
        }
        static::$_binding[$abstract] = compact('content', 'singleton');
    }


    /**
     * binding class and share
     * @param $abstract
     * @param null $content
     * @return mixed
     */
    public function &share($abstract, $content = null)
    {
        $this->bind($abstract, $content, true);
        return $this->make($abstract);
    }

    /**
     * set instance
     * @param $abstract
     * @param $content
     * @return $this
     */
    public function instance($abstract, $content)
    {
        static::$_instances[$abstract] = $content;
        return $this;
    }

    /**
     * get method Parameters
     * @param $methodObject
     * @param array $parameters
     * @return array
     */
    protected function getMethodParameters($methodObject, $parameters = [])
    {
        if ($methodObject instanceof ReflectionMethod || $methodObject instanceof ReflectionFunction) {
            $methodObject = $methodObject->getParameters();
        }
        $dependencies = [];
        $index = 0;

        foreach ($methodObject as $methodParameter) {

            if (array_key_exists($methodParameter->name, $parameters)) {
                $dependencies[$methodParameter->name] = $parameters[$methodParameter->name];
            } else {
                $class = $methodParameter->getClass();
                if (is_object($class)) {
                    if($class->name == 'Closure'){
                        $Closure = Arr::first($parameters,function ($key,$val) use(&$parameters){
                            unset($parameters[$key]);
                            return ($val instanceof Closure);
                        });
                        $dependencies[] = $Closure;
                    }else {
                        $dependencies[] = &$this->make($class->name);
                    }
                } elseif (isset($parameters[$index])) {
                    $dependencies[] = $parameters[$index];
                    unset($parameters[$index]);
                    $index++;
                } elseif ($methodParameter->isDefaultValueAvailable()) {
                    $dependencies[] = $methodParameter->getDefaultValue();
                } else {
                    $dependencies[] = array_shift($parameters);
                }
            }
        }
        if (empty($dependencies) && !empty($parameters)) {
            $dependencies = $parameters;
        }

        return $dependencies;
    }

    /**
     * create class with parameters
     * @param $abstract
     * @param array $parameters
     * @return object
     */
    public function getClass($abstract, $parameters = [])
    {

        if (($abstract instanceof Closure) || ($abstract === 'Closure')) {
            return $abstract($this, $parameters);
        }

        $class = new ReflectionClass($abstract);
        if ($class->isInstantiable()) {
            $constructor = $class->getConstructor();
            if (is_object($constructor)) {
                $parameters = $this->getMethodParameters($constructor, $parameters,$abstract);
            }

            return $class->newInstanceArgs($parameters);
        }
        return $class->newInstance($parameters);
    }

    /**
     * get method
     * @param $abstract
     * @return \ReflectionFunction|\ReflectionMethod
     */
    protected function getMethod($abstract)
    {
        if (is_array($abstract)) {
            return new ReflectionMethod($abstract[0], $abstract[1]);
        }
        return new ReflectionFunction($abstract);
    }

    /**
     * call method or Closure
     * @param $abstract
     * @param array $parameters
     * @return mixed|object
     */
    public
    function call($abstract, $parameters = [])
    {
        if (!is_array($abstract) && !($abstract instanceof Closure)) {
            return $this->getClass($abstract, $parameters);
        }

        $method = $this->getMethod($abstract);
        $parameters = $this->getMethodParameters($method, $parameters);
        if (is_array($abstract) && !is_object($abstract[0])) {
            $abstract[0] = &$this->make($abstract[0]);
        }
        return call_user_func_array($abstract, $parameters);
    }

    /**
     * create object
     * @param $abstract
     * @param array $parameters
     * @return mixed
     * @throws HandlerExceptions
     */
    public
    function &make($abstract, $parameters = [])
    {
        if ($this->loader->isAlias($abstract)) {
            $abstract = $this->loader->getAlias($abstract);
        }

        if (array_key_exists($abstract, static::$_instances)) {
            return static::$_instances[$abstract];
        }

        $content = null;
        $singleton = false;
        if (isset(static::$_binding[$abstract])) {
            $content = static::$_binding[$abstract]['content'];
            $singleton = static::$_binding[$abstract]['singleton'];
        } else {
            $content = $abstract;
        }
        $content = $this->getClass($content, $parameters);
        if ($singleton) {
            static::$_instances[$abstract] = $content;
        }
        return $content;
    }

    /**
     * get element
     * @param $name
     * @return mixed
     * @throws MyException
     */

    public
    function __get($name)
    {
        return $this[$name];
    }

    /**
     * set new item with value
     * @param $name
     * @param $value
     */
    public
    function __set($name, $value)
    {
        $this[$name] = $value;
    }

    /**
     * @param $name
     * @return bool
     */
    protected
    function check($name)
    {
        return (array_key_exists($name, static::$_instances) || array_key_exists($name, static::$_binding) || $this->loader->isAlias($name));
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public
    function offsetExists($offset)
    {
        return $this->check($offset);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public
    function offsetGet($offset)
    {
        return $this->make($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public
    function offsetSet($offset, $value)
    {
        if (!$value instanceof Closure) {
            $value = function () use ($value) {
                return $value;
            };
        }
        $this->bind($offset, $value);
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public
    function offsetUnset($offset)
    {
        unset(static::$_instances[$offset], static::$_binding[$offset]);
    }
}