<?php
/**
 * -----------------------------------
 * File  : DatabasesService.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Databases;



use ArPHP\Services\Services;

class DatabasesService extends Services
{

    /**
     * register settings
     * @return mixed
     */
    public function register()
    {
        $this->app->bind(Connection::class,function ($app,$args = []){
            return call_user_func_array([Connection::class,'getConnection'],$args);
        });
    }
}