<?php
/**
 * -----------------------------------
 * File  : Repository.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Config;

/**
 * Class Repository
 * @package ArPHP\Config
 */
class Repository extends \ArPHP\Support\Repository
{
    /**
     * default  path
     * @var array|mixed
     */
    protected $paths   = [];

    /**
     * Repository constructor.
     */
    public function __construct()
    {
        parent::__construct([]);
        $this->paths[] = config_path() ;
    }

    /**
     * set modules paths
     * @param $alias
     * @param $path
     */
    public function module($alias,$path){
        $this->paths = array_merge((array)$this->paths,[$alias => $path]);
    }

    /**
     * load config file
     * @param $key
     * @param bool $force
     * @return bool
     */
    protected function load($key,$force = false){
        $array = explode('.',trim($key,'. '),2) ;
        $file = $array[0];
        if(array_key_exists($file,$this->items) && $force === false){
            return true;
        }
        $path = $this->paths ;
        $module = null;
        if(($module = strstr($file,'::',true)) != false){
            if(array_key_exists($module,$this->paths)){
                $path = $this->paths[$module];
            }else{
                return false;
            }
            $file = ltrim(strstr($file,'::'),'::');
        }
        $module = null;
        foreach ((array)$path as $route) {
            if(is_file($filename = rtrim($route,DS).DS.$file.EXT)){
                $this->items[$array[0]] = require "$filename";
                return true;
            }
        }
        return false;
    }

    /**
     * get item
     * @param string $key
     * @param null $default
     * @param bool $force
     * @return mixed
     */
    public function get($key, $default = null,$force = false)
    {
        $this->load($key,$force);
        return parent::get($key, $default);
    }

    /**
     * set item
     * @param array|string $key
     * @param null $value
     */
    public function set($key, $value = null)
    {
        $this->load($key);
        parent::set($key, $value);
    }
}