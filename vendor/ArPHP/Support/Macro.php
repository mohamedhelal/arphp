<?php
/**
 * -----------------------------------
 * File  : Macro.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Support;
use ArPHP\Exceptions\MyException;
use Closure;

abstract class Macro
{
    /**
     * The registered string macros.
     *
     * @var array
     */
    protected static $macros = [];

    /**
     * Register a custom macro.
     *
     * @param  string    $name
     * @param  callable  $macro
     * @return void
     */
    public static function macro($name, callable $macro)
    {
        static::$macros[$name] = $macro;
    }

    /**
     * Checks if macro is registered.
     *
     * @param  string  $name
     * @return bool
     */
    public static function hasMacro($name)
    {
        return isset(static::$macros[$name]);
    }

    /**
     * Dynamically handle calls to the class.
     * @param string $method
     * @param array $parameters
     * @return mixed
     * @throws MyException
     */
    public static function __callStatic($method, $parameters)
    {
        if (static::hasMacro($method)) {
            $callback = static::$macros[$method];
            if ($callback instanceof Closure) {
                return call_user_func_array(Closure::bind($callback, null, get_called_class()), $parameters);
            } else {
                return call_user_func_array($callback, $parameters);
            }
        }

        throw new MyException("Method {$method} does not exist.");
    }

    /**
     * Dynamically handle calls to the class.
     * @param string $method
     * @param array $parameters
     * @return mixed
     * @throws MyException
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            $callback = static::$macros[$method];
            if ($callback instanceof Closure) {
                return call_user_func_array($callback->bindTo($this, get_class($this)), $parameters);
            } else {
                return call_user_func_array($callback, $parameters);
            }
        }

        throw new MyException("Method {$method} does not exist.");
    }
}