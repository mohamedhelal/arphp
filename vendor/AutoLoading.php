<?php
/**
 * -----------------------------------
 * File  : AutoLoading.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */
/**
 * static vars
 */
require_once 'ArPHP/Application/constants.php';

/**
 * Class AutoLoading
 * loading classes
 */
class AutoLoading
{
    /**
     * all namespaces
     * @var array
     */
    protected $_namespaces = [];
    /**
     * all classes files
     * @var array
     */
    protected $_classMaps = [];
    /**
     * all classes aliases
     * @var array
     */
    protected $_aliases = [];
    /**
     * @var \ArPHP\Exceptions\Handler
     */
    protected $handler;

    /**
     * AutoLoading constructor.
     * @param $namespaces
     */
    public function __construct($namespaces)
    {
        $this->setNamespace($namespaces);
        spl_autoload_register([$this, 'ClassesLoader']);
    }

    /**
     * get handler object
     * @return \ArPHP\Exceptions\Handler
     */
    public function &getHandler()
    {
        return $this->handler;
    }
    /**
     * check if is alias exists
     * @param $abstract
     * @return bool
     */
    public function isAlias($abstract)
    {
        return (isset($this->_aliases[$abstract]) ? true : false);
    }

    /**
     * get alias class
     * @param $abstract
     * @return mixed
     */
    public function getAlias($abstract)
    {
        return $this->_aliases[$abstract];
    }

    /**
     * load classes file
     * @param $class
     * @return mixed
     */
    public function ClassesLoader($class)
    {
        if (isset($this->_aliases[$class])) {
            return class_alias($this->_aliases[$class], $class);
        } elseif (isset($this->_classMaps[$class])) {
            return require_once($this->_classMaps[$class]);
        } else {
            foreach ($this->_namespaces as $namespace => $path) {
                if (startsWith($class, $namespace)) {
                    return $this->loadClass(substr($class, strlen($namespace)), $path);
                }
            }
        }
        return $this->loadClass($class);
    }

    /**
     * load class
     * @param $class
     * @param null $path

     * @return bool|mixed
     */
    protected function loadClass($class, $path = null)
    {
        $path = ($path == null ? null : rtrim($path, DS) . DS);
        $class_path = str_replace([(strpos($class,'\\') === false ? '_':'\\'), DS], DS, $class);
        $files = [
            $path . $class_path . EXT
        ];
        foreach ($files as $file) {
            if (is_file($file)) {
                return require_once($file);
            }
        }
        return false;
    }

    /**
     * set namespace
     * @param $namespaces
     * @param null $path
     * @return $this
     */
    public function setNamespace($namespaces, $path = null)
    {
        if (!is_array($namespaces)) {
            $namespaces = [$namespaces => $path];
        }
        foreach ($namespaces as $namespace => $path) {
            if (!isset($this->_namespaces[$namespace])) {
                $this->_namespaces[$namespace] = rtrim($path, DS) . DS;
            }
        }
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
        if (!is_array($namespaces)) {
            $namespaces = [$namespaces => $file];
        }
        foreach ($namespaces as $namespace => $file) {
            if (!isset($this->_classMaps[$namespace])) {
                $this->_classMaps[$namespace] = $file;
            }
        }
        return $this;
    }

    /**
     * set classes aliases
     * @param $aliases
     * @param null $class
     * @return $this
     */
    public function setAlias($aliases, $class = null)
    {
        if (!is_array($aliases)) {
            $aliases = [$aliases => $class];
        }
        foreach ($aliases as $namespace => $class) {
            if (!isset($this->_aliases[$namespace])) {
                $this->_aliases[$namespace] = $class;
            }
        }
        return $this;
    }

    /**
     * create Application
     * @param $basePath
     * @return \ArPHP\Application\Application
     */
    public static final function &get($basePath)
    {
        $basePath = realpath($basePath).DS;
        $namespaces = [
            'ArPHP\\' => __DIR__.DS . 'ArPHP' . DS,
            'App\\' =>  $basePath .'app' . DS,
            'Modules\\' => $basePath.'modules' . DS,
        ];
        $loader = new AutoLoading($namespaces);
        require_once 'ArPHP/Application/helper.php';
        $app =  \ArPHP\Application\Application::create($basePath,$loader);
        return $app;
    }

}