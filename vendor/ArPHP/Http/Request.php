<?php
/**
 * -----------------------------------
 * File  : Request.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Http;
use ArPHP\Filesystem\Upload\Upload;
use ArPHP\Routing\Route;
use ArPHP\Routing\Router;
use ArPHP\Sessions\SessionManager;
use ArPHP\Support\Arr;
use ArPHP\Support\Macro;
use ArPHP\Support\Repository;


/**
 * Class Request
 * @package ArPHP\Http
 */
class Request extends Macro
{

    /**
     * @var UrlGenerator
     */
    protected $url;

    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->url = app(UrlGenerator::class);
    }

    /**
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if(method_exists($this->url,$method)){
            return call_user_func_array([$this->url,$method],$parameters);
        }

        return parent::__call($method,$parameters);
    }

    /**
     * get query string url
     * @return Access|mixed
     */
    public function queryString()
    {
        return $this->get(__QUERY_STRING__);
    }

    /**
     * get url string
     * @return string
     */
    public function path()
    {
        return (isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : (isset($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : getenv('PATH_INFO')));
    }

    /**
     * if request  from ajax
     * @return bool
     */
    public function isAjax()
    {
        return ($this->server('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest');
    }

    /**
     * if request from post
     * @return bool
     */
    public function isPost()
    {
        return ($this->server('REQUEST_METHOD') === 'POST');
    }

    /**
     * if request from get
     * @return bool
     */
    public function isGet()
    {
        return ($this->server('REQUEST_METHOD') === 'GET');
    }


    /**
     * check if request is secure
     * @return bool
     */
    public function isHttps()
    {
        return ($this->server('REQUEST_SCHEME') === 'https');
    }

    /**
     * get post array
     * @return Access
     */
    public function post()
    {
        $post = new Access($_POST);
        if (count(func_get_args()) > 0) {
            return call_user_func_array([$post, 'get'], func_get_args());
        }
        return $post;
    }
    /**
     * upload files
     * @param $name
     * @return Upload|Repository
     */
    public function file($name){
        $items = array();
        $one = true;
        if (Arr::has($_FILES, $name)) {
            $files = $_FILES[$name];
            if (is_array($files['name'])) {
                $counter = count($files['name']);
                for ($i = 0; $i < $counter; $i++) {
                    if (!isset($files['name'][$i]) || empty($files['name'][$i])) {
                        continue;
                    }
                    $file = array(
                        'name' => (string)$files['name'][$i],
                        'type' => (string)$files['type'][$i],
                        'tmp_name' => (string)$files['tmp_name'][$i],
                        'error' => (string)$files['error'][$i],
                        'size' => (string)$files['size'][$i],
                    );
                    $items[] = new Upload($file);
                }
                $one = false;
            } else {
                $items[] = new Upload($files);
            }
        }
        $Collection = new Repository($items);
        if ($one) {
            $Collection = $Collection->first();
        }
        return $Collection;
    }
    /**
     * get server array
     * @return Access|mixed
     */
    public function server()
    {
        $post = new Access($_SERVER);
        if (count(func_get_args()) > 0) {
            return call_user_func_array([$post, 'get'], func_get_args());
        }
        return $post;
    }

    /**
     * get get array
     * @return Access|mixed
     */
    public function get()
    {
        $post = new Access($_GET);
        if (count(func_get_args()) > 0) {
            return call_user_func_array([$post, 'get'], func_get_args());
        }
        return $post;
    }
    /**
     * @param $index
     * @param bool $default
     * @return bool
     */
    public function segment($index, $default = false)
    {
        return $this->route()->segment($index, $default);
    }
    /**
     * @return Route
     */
    public function route(){
        return app(Router::class)->current();
    }

    /**
     * get domain
     * @param bool $secure
     * @return string
     */
    public function domain($secure = false){
        $host = $this->server('HTTP_HOST');
        $file = $this->server('SCRIPT_NAME');
        $url = ($secure ? 'https' : 'http') . '://' . $host . str_replace(basename($file), '', $file);
        return $url;
    }
    /**
     * get request method
     * @return string
     */
    public function method()
    {
        if (strtoupper($this->server('REQUEST_METHOD')) == "POST") {
            if ($this->post()->has(__USER_METHOD__)) {
                return $this->post(__USER_METHOD__);
            }
        }
        return $this->server('REQUEST_METHOD');
    }

    /**
     * @param $url
     * @return int
     */
    public function is($url){
        $url = str_replace('*','(.*)',$url);
        return preg_match('#^'.ltrim($url,'/').'$#u',ltrim(str_replace(base_url(),null,$this->url->fullUrl()),'/'));
    }
    /**
     * @return mixed
     */
    public function all()
    {
        $all = array_merge($this->get()->all(),$this->post()->all(),$this->route()->getSegments());
        $args = [$all];
        $args = array_merge($args,func_get_args());
        return call_user_func_array([Arr::class,'get'],$args);
    }

    /**
     * get old post data
     * @param bool|false $key
     * @return mixed
     */
    public function old($key = false)
    {
        $old = \Session::get(_OLD_INPUT_DATA, array());
        return Arr::get($old, $key);
    }

    /**
     * get form token
     * @return bool
     */
    public function token(){
        $token = SessionManager::TOKEN;
        return ($this->get($token)?:($this->post($token)?:false));
    }



    /**
     * @param $name
     * @return bool
     */
    public function isRouteName($name)
    {
        return ($this->route()->getName() === $name);
    }

    /**
     * @param $url
     * @return bool
     */
    public function isRouteUrl($url)
    {
        return ($this->route()->getUri() === $url);
    }

    /**
     * @param $action
     * @return bool
     */
    public function isRouteAction($action)
    {
        return ($this->route()->getAction() === $action);
    }

    /**
     * @param $controller
     * @return bool
     */
    public function isRouteController($controller)
    {
        return ($this->route()->getController() === $controller);
    }

    /**
     * @param $method
     * @return bool
     */
    public function isRouteMethod($method)
    {
        return ($this->route()->getMethod() === $method);
    }
}