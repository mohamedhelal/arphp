<?php
/**
 * -----------------------------------
 * File  : ModulesServices.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace App\Services;


use ArPHP\Services\Services;

class ModulesServices extends Services
{

    /**
     * register settings
     * @return mixed
     */
    public function register()
    {
       $enabled = config('modules.enabled');
        foreach ($enabled as $item) {
            $class = "Modules\\$item\\Services\\RouteServices";
            if(class_exists($class)){
                $this->app->registerService($class);
            }
       }

    }
}