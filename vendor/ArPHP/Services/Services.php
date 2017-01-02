<?php
/**
 * -----------------------------------
 * File  : Services.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Services;


abstract class Services
{
    /**
     * @var \ArPHP\Application\Application|mixed
     */
    protected $app;

    /**
     * Services constructor.
     */
    public final function __construct()
    {
        $this->app = app();
    }
    /**
     * register settings
     * @return mixed
     */
    abstract public function register();
}