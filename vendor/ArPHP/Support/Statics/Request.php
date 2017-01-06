<?php
/**
 * -----------------------------------
 * File  : Request.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Support\Statics;


class Request extends Statics
{

    /**
     * get object name
     * @return mixed
     */
    protected static function getStaticName()
    {
        return \ArPHP\Http\Request::class;
    }
}