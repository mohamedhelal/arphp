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
        $file = $this->file; $line = $this->line;
        if($file == false && $line == false) {
            $debug_backtrace = debug_backtrace();
        }else{
            $debug_backtrace = $this->getTrace();
        }
        foreach ($debug_backtrace as $index => $item) {
            if ($index >= 1 && isset($item['file'], $item['line'])) {
                $file = $item['file'];
                $line = $item['line'];
                break;
            }
        }
        parent::__construct($message, 8, $file, $line);
    }
}