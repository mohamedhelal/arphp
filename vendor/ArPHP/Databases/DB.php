<?php
/**
 * -----------------------------------
 * File  : DB.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Databases;


use ArPHP\Support\Statics\Statics;

class DB extends Statics
{

    /**
     * get object name
     * @return mixed
     */
    protected static function getStaticName()
    {
        return Connection::class;
    }
}