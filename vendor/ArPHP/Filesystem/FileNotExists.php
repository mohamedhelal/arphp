<?php
/**
 * -----------------------------------
 *  project name myshop
 * -----------------------------------
 * File:FileNotExists.php
 * User: mohamed
 */

namespace ArPHP\Filesystem;


use ArPHP\Exceptions\MyException;

class FileNotExists extends MyException
{
    public function __construct($message)
    {
        parent::__construct($message, E_NOTICE, false, false);
    }
}