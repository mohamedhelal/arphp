<?php
/**
 * -----------------------------------
 *  project name framework
 * -----------------------------------
 * File:Messages.php
 * User: mohamed
 */
namespace ArPHP\Validation;
/*
*----------------------------------------
* Class Name  Messages 
*----------------------------------------
*/
class Messages
{
    protected $errors = array();

    /**
     * set errors
     * @param $errors
     */
    public function __construct(&$errors)
    {
        $this->errors = &$errors;
    }

    /**
     * if errors has field name
     * @param $field
     * @return bool
     */
    public function has($field)
    {
        return (isset($this->errors[$field])?$this->errors[$field]:false);
    }

    /**
     * get all error
     * @return array
     */
    public function all(){
        return $this->errors;
    }
    /**
     * get errors
     * @param bool $implode
     * @return array|string
     */
    public function get($implode = false)
    {

        if ($implode != false) {
            $str = '';
            array_map(function ($error) use ($implode, &$str) {
                $str .= str_replace(':error', $error, $implode);
            }, $this->errors);
            return $str;
        }
        return $this->errors;
    }
}