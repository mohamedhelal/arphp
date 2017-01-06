<?php
/**
 * -----------------------------------
 * File  : FilesystemService.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */


namespace ArPHP\Filesystem;



use ArPHP\Services\Services;

class FilesystemService extends Services
{

    /**
     * register services
     * @return mixed
     */
    public function register()
    {
        $this->app->share(Filesystem::class,Filesystem::class);
    }
}