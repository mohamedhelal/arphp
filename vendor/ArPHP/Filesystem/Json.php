<?php
/**
 * -----------------------------------
 *  project name myshop
 * -----------------------------------
 * File:Json.php
 * User: mohamed
 */

namespace ArPHP\Filesystem;



use ArPHP\Support\Repository;

class Json extends Repository
{
    public function __construct($file){
        if(!is_file($file)){
            throw new FileNotExists('File :'.$file.' Not Exists');
        }
        $json = (array)json_decode(file_get_contents($file));
        $json = array_change_key_case($json,CASE_LOWER);
        parent::__construct($json);
    }
}