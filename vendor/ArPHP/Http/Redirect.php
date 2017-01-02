<?php
/**
 * -----------------------------------
 * File  : Redirect.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Http;

use Session;
class Redirect
{
    protected $action;
    protected $time = 0;

    /**
     * set redirect data
     * @param $action
     * @param int $time
     */
    public function __construct($action = null, $time = 0)
    {
        $this->action = $action;
        $this->time = $time;
    }

    /**
     * redirect by route name
     * @param $name
     * @param array $parameters
     * @return Redirect
     */
    public  function route($name,$parameters = array()){
        return new static(route($name,$parameters),0);
    }

    /**
     * redirect by route action
     * @param $action
     * @param array $parameters
     * @return Redirect
     */
    public  function action($action,$parameters = array()){
        return new static(action($action,$parameters),0);
    }
    /**
     * redirect by url
     * @param bool|false $url
     * @return Redirect
     */
    public  function url($url = false){
        return new static(url($url),0);
    }
    /**
     * go back
     * @return Redirect
     */
    public  function back(){
        return static::url(request()->server('HTTP_REFERER'));
    }

    /**
     * @param $name
     * @param $value
     * @return Redirect
     */
    public function with($name,$value){
        Session::flash($name,$value);
        return $this;
    }

    /**
     * set data to flash
     * @param null $data
     * @return $this
     */
    public function withInput($data = null){
        $data = ($data == null ? request()->post()->all() : $data);
        Session::flash(_OLD_INPUT_DATA,$data);
        return $this;
    }

    /**
     * @param $success
     * @return Redirect
     */
    public function success($success){
        return $this->with('success',$success);
    }


    /**
     * @param $warning
     * @return Redirect
     */
    public function warning($warning){
        return $this->with('warning',$warning);
    }


    /**
     * @param $error
     * @return Redirect
     */
    public function error($error){
        return $this->with('error',$error);
    }

    /**
     * @param $info
     * @return Redirect
     */
    public function info($info){
        return $this->with('info',$info);
    }



    /**
     * get class to string
     * @return string
     */
    public function __toString(){
        ob_start();
        header('Refresh: ' . $this->time . ';' . $this->action);
        return  ob_get_clean();
    }

}