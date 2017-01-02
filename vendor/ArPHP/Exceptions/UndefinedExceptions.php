<?php
/**
 * -----------------------------------
 * File  : UndefinedExceptions.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Exceptions;


class UndefinedExceptions extends HandlerExceptions
{
    public function __construct($message)
    {
        $debug_backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
        $file = $debug_backtrace[1]['file'];
        $line = $debug_backtrace[1]['line'];
        parent::__construct($message, 8, $file, $line);
    }
}