<?php
/**
 * -----------------------------------
 *  project name framework
 * -----------------------------------
 * File:HtmlCollection.php
 * User: mohamed
 */

namespace ArPHP\Html;




use ArPHP\Support\Facade\Facade;

class HtmlCollection extends Facade
{


    /**
     * get object name
     * @return mixed
     */

    protected static function getInstanceName()
    {
        return 'html';
    }
}