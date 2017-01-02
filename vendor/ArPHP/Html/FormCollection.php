<?php
/**
 * -----------------------------------
 *  project name framework
 * -----------------------------------
 * File:FormCollection.php
 * User: mohamed
 */

namespace ArPHP\Html;





use ArPHP\Support\Facade\Facade;

class FormCollection extends Facade
{

    /**
     * get object name
     * @return mixed
     */


    protected static function getInstanceName()
    {
        return 'form';
    }
}