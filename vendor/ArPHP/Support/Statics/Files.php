<?php
/**
 * -----------------------------------
 * File  : Files.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Support\Statics;


use ArPHP\Filesystem\Filesystem;

class Files extends Statics
{

    /**
     * get object name
     * @return mixed
     */
    protected static function getStaticName()
    {
        return Filesystem::class;
    }
}