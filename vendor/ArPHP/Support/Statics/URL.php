<?php
/**
 * -----------------------------------
 * File  : URL.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Support\Statics;


use ArPHP\Http\UrlGenerator;

class URL extends Statics
{

    /**
     * get object name
     * @return mixed
     */
    protected static function getStaticName()
    {
        return UrlGenerator::class;
    }
}