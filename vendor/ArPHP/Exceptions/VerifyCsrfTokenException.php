<?php
/**
 * -----------------------------------
 * File  : VerifyCsrfTokenException.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Exception;


use ArPHP\Exceptions\MyException;

class VerifyCsrfTokenException extends MyException
{
    public function __construct()
    {
        parent::__construct('Token Not Match', 404, false, false);
    }
}