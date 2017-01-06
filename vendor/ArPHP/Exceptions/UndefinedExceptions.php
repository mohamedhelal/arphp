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
        foreach ($debug_backtrace as $index => $item) {
            if($index > 1 || !isset($item['file'],$item['line'])){
                continue;
            }
            $file = $item['file'];
            $line = $item['line'];
            break;
        }

        parent::__construct($message, 8, $file, $line);
    }
}