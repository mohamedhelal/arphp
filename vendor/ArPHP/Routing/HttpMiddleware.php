<?php
/**
 * -----------------------------------
 * File  : HttpMiddleware.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Routing;


abstract class HttpMiddleware
{
    /**
     * all this route middleware
     * @var array
     */
    protected $middleware = [];


    /**
     * get all  this route middleware
     * @return array
     */
    public final function getMiddleware(){
        return $this->middleware;
    }
}