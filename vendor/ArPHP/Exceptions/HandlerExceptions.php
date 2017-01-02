<?php
/**
 * -----------------------------------
 * File  : HandlerExceptions.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Exceptions;


class HandlerExceptions extends MyException
{
    public function __construct($message, $code,$file,$line)
    {
        parent::__construct($message, $code);
        $this->file = $file;
        $this->line = $line;
    }
}