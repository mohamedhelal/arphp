<?php
/**
 * -----------------------------------
 * File  : SessionServices.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Sessions;


use ArPHP\Http\Request;
use ArPHP\Services\Services;

class SessionServices extends Services
{

    /**
     * register settings
     * @return mixed
     */
    public function register()
    {
        $sessions = &$this->app->share(SessionManager::class, function () {
            return new SessionManager();
        });
        Request::macro('sessions',function () use (&$sessions) {
            return $sessions;
        });

    }
}
