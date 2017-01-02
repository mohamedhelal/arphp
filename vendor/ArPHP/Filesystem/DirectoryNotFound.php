<?php
/**
 * -----------------------------------
 *  project name framework
 * -----------------------------------
 * File:DirectoryNotFound.php
 * User: mohamed
 */

namespace ArPHP\Filesystem;



use ArPHP\Exceptions\UndefinedExceptions;

class DirectoryNotFound extends UndefinedExceptions
{
    public function __construct($message)
    {
        parent::__construct($message, E_NOTICE, false, false);
    }
}