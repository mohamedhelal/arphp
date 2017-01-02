<?php
/**
 * -----------------------------------
 * File  : Repository.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Translate;
/**
 * Class Repository
 * @package ArPHP\Translate
 */
class Repository extends \ArPHP\Config\Repository
{
    /**
     * @var string
     */
    protected $local ;
    /**
     * Repository constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->paths = lang_path();
    }

    /**
     * set local lang
     * @param $local
     */
    public function setLocal($local){
        $this->local = $local;
    }

    /**
     * @return string
     */
    public function getLocal(){
        return $this->local;
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
        if($force === false && array_key_exists($file,$this->items)){
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
            $route = rtrim($route,DS).DS.(is_string($this->local) ? $this->local.DS:null);
            if(is_file($filename = $route.$file.EXT)){
                $this->items[$array[0]] = require "$filename";
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $key
     * @param array $replace
     * @param bool $force
     * @return mixed
     */
    public function get($key, $replace = [],$force = false)
    {
        $val = parent::get($key, $key,$force);
        if(count($replace)){
            foreach ($replace as $name => $item) {
                $val = str_replace(':'.$name, $item, $val);
            }
        }
        return $val;
    }
}