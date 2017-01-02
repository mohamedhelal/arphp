<?php
/**
 * -----------------------------------
 * File  : HttpException.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Routing;


use ArPHP\Exceptions\MyException;

class HttpException extends MyException
{
    public function __construct()
    {
        parent::__construct('Sorry, the page you are looking for could not be found.', 404);
    }
}