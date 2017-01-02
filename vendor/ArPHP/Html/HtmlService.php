<?php
/**
 * -----------------------------------
 * File  : HtmlService.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */


namespace ArPHP\Html;



use ArPHP\Services\Services;

class HtmlService extends Services
{

    /**
     * register services
     * @return mixed
     */
    public function register()
    {
        $this->app['html'] = new Html();
        $this->app['form'] = new Form();
    }
}