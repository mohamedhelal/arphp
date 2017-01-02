<?php
/**
 * -----------------------------------
 * File  : MyException.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Exceptions;




class MyException extends \Exception
{

    public function __construct($message, $code = 2)
    {
        parent::__construct($message, $code);
    }

    /**
     * @return mixed
     */
    public function getStatusCode(){
        return $this->code;
    }
}