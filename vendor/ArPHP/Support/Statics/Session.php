<?php
/**
 * -----------------------------------
 * File  : Session.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Support\Statics;


use ArPHP\Sessions\SessionManager;

class Session extends Statics
{

    /**
     * get object name
     * @return mixed
     */
    protected static function getStaticName()
    {
        return SessionManager::class;
    }
}