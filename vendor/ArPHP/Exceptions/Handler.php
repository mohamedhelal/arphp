<?php
/**
 * -----------------------------------
 * File  : Handler.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Exceptions;
use ArPHP\Databases\DatabaseException;
use ArPHP\Routing\HttpException;

/**
 * Class Handler
 * handler all errors
 * @package ArPHP\Exceptions
 */
class Handler
{
    /**
     * environment type
     */
    const  ENVIRONMENT = 'production';
    /**
     * error types
     * @var array
     */
    protected static   $errorType = array(
        E_ERROR => 'ERROR',
        E_WARNING => 'WARNING',
        E_PARSE => 'PARSING ERROR',
        E_NOTICE => 'NOTICE',
        E_CORE_ERROR => 'CORE ERROR',
        E_CORE_WARNING => 'CORE WARNING',
        E_COMPILE_ERROR => 'COMPILE ERROR',
        E_COMPILE_WARNING => 'COMPILE WARNING',
        E_USER_ERROR => 'USER ERROR',
        E_USER_WARNING => 'USER WARNING',
        E_USER_NOTICE => 'USER NOTICE',
        E_STRICT => 'STRICT NOTICE',
        E_RECOVERABLE_ERROR => 'RECOVERABLE ERROR'
    );
    /**
     * Handler constructor.
     */
    public function __construct()
    {
        foreach (static::$errorType as $key => $value) {
            static::$errorType[$key] = ucwords(strtolower($value));
        }
        error_reporting(-1);
        register_shutdown_function(array($this, 'register_shutdown_function'));
        set_error_handler([$this, 'set_error_handler']);
        set_exception_handler(array($this, 'set_exception_handler'));
    }

    /**
     * @param $level
     * @param $error
     * @param $file
     * @param $line
     * @return string
     */
    public function set_error_handler($level, $error, $file, $line)
    {
        return $this->prepare(new HandlerExceptions($error, $level, $file, $line));
    }


    /**
     * run time handler error exceptions
     */
    public function register_shutdown_function()
    {
        $error = error_get_last();
        if (!is_null($error)) {
            $this->set_error_handler($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }

    /**
     * handler exception
     * @param $exception
     * @return string
     */
    public function set_exception_handler($exception)
    {
         $this->prepare($exception);
    }

    /**
     * get class name
     * @param  $e
     * @return string
     */
    public function getTitle( $e)
    {
        if($e->getCode() == 404){
            return '404 error';
        }elseif($e instanceof DatabaseException){
            return 'Database Exception';
        }
        return (array_key_exists($e->getCode(),static::$errorType) ? static::$errorType[$e->getCode()].' Error':'Fatal Error');
    }

    /**
     * show files trace
     * @param  $e
     * @return bool
     */
    public function showTrace( $e)
    {
        if($e instanceof HttpException){
            return false;
        }elseif($e instanceof \ArPHP\Databases\DatabaseException){
            return true;
        }
        return true;
    }

    /**
     * @param  $e
     * @return mixed|string
     */
    public function getErrorType( $e){
        return (array_key_exists($e->getCode(),static::$errorType) ? static::$errorType[$e->getCode()].' Error':'Fatal Error');

    }
    /**
     * @param $args
     * @return string
     */
    public function getArrayOrObjectToString($args){
        $newArgs = [];
        foreach ($args as $arg) {
            if(is_object($arg)){
                $newArgs[] = 'object('.class_name(get_class($arg)).')';
            }elseif (is_array($arg)){
                $arg = $this->getArrayOrObjectToString($arg);
                $newArgs[] = 'array'.$arg;
            }else{
                $newArgs[] = str_replace(base_path(),'',$arg);;
            }
        }
        return '('.implode(',',$newArgs).')';
    }

    /**
     * @param $e
     * @return array
     */
    public function getTrace( $e){
        $getTrace = $e->getTrace();
        $errors = [];
        $names = ['class','type','function','args','file'];
        foreach ($getTrace as $item) {
            $li = '';
            foreach ($names as $name) {
                if(isset($item[$name])){
                    if($name == 'args'){
                        $args = $item[$name];
                        $li.= $this->getArrayOrObjectToString($args);
                    }elseif ($name == 'file'){
                        $item[$name] = str_replace(base_path(),'',$item[$name]);
                        $li .= ' <b>in</b> <i class="file">'.$item[$name].' line '.$item['line'].'</i>';
                    }elseif($name == 'class'){
                        $li.= '<u title="'.$item[$name].'">'.class_name($item[$name]).'</u>';
                    }
                    else{
                        $li.= $item[$name];
                    }
                }
            }
            $errors[] = $li;
        }
        return $errors;
    }
    /**
     * @param  $e
     * @return string
     */
    protected function prepare( $e)
    {

        while (ob_get_level() != 0){
            ob_end_clean();
        }
        include 'view.php';
        die();
    }
}