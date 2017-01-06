<?php
/**
 * -----------------------------------
 * File  : UrlGenerator.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Http;



use ArPHP\Routing\Route;
use ArPHP\Routing\Router;

class UrlGenerator
{

    /**
     * Characters that should not be URL encoded.
     *
     * @var array
     */
    protected $dontEncode = array(
        '%2F' => '/',
        '%40' => '@',
        '%3A' => ':',
        '%3B' => ';',
        '%2C' => ',',
        '%3D' => '=',
        '%2B' => '+',
        '%21' => '!',
        '%2A' => '*',
        '%7C' => '|',
        '%3F' => '?',
    );
    /**
     * raw url encode url
     * @param $url
     * @return string
     */
    public function rawUrlEncode($url)
    {
        $explode = explode('/', $url);
        foreach ($explode as $index => $val) {
            if ($index == 0) {
                continue;
            }
            $explode[$index] = rawurlencode($val);
        }
        $link = implode('/', $explode);
        return str_replace(array_keys($this->dontEncode), array_values($this->dontEncode), $link);
    }

    /**
     * @param $url
     * @return bool
     */
    public function isUrl($url)
    {
        if (startsWith(strtolower($url), array('http://', 'https://', 'ftp://'))) {
            return true;
        }
        return false;
    }

    /**
     * @return Access|mixed
     */
    public function getDomain()
    {
        return ltrim(request()->server('SERVER_NAME'), 'www.');
    }

    /**
     * @return bool
     */
    public function getHttps()
    {
        return (strtolower(request()->server('REQUEST_SCHEME')) === 'https');
    }


    /**
     * get url base
     * @param bool|false $path
     * @param bool|false $secure
     * @return string
     */
    public function base($path = false, $secure = false)
    {
        $url = request()->domain($secure);
        $app_url = config('app.url');
        if (!empty($app_url)) {
            $url = $app_url;
        }
        $url .= ($path != false ? rtrim(str_replace([base_path(), '\\'], [null, '/'], $path), '\ /') . '/' : null);
        return $url;
    }

    /**
     * get url
     * @param bool|false $url
     * @param bool|false $secure
     * @return bool|string
     */
    public function url($url = false, $secure = false)
    {
        if ($this->isUrl($url)) {
            return $url;
        }
        $base = $this->base(false, $secure) ;
        if (config('app.queryString') && $url != false) {
            $base .= '?'.__QUERY_STRING__.'=';
        }
        $base = $base. ltrim($url, '/ ');
        return $base;
    }

    /**
     * get current route url
     * @return string
     */
    public function current(){
        $route = request()->route();
        return $this->toRoute($route,$route->getSegments());
    }

    /**
     * get full url with query string
     * @return string
     */
    public function fullUrl(){
        $url = $this->current();
        if(count($_GET)){
            $url .= '?'.http_build_query($_GET);
        }
        return $url;
    }
    /**
     * get route by name
     * @param $name
     * @param array $parameters
     * @param null $route
     * @return string
     * @throws HttpRouteException
     */
    public function route($name,$parameters = [],$route = null){
        $route = $route?:app(Router::class)->findRouteByName($name);
        $parameters = (array) $parameters;
        if(!is_null($route)){
            return $this->toRoute($route,$parameters);
        }
        throw new HttpRouteException('Undefined Route  "' . $name . '"');
    }

    /**
     * get route by action
     * @param $action
     * @param array $parameters
     * @return string
     */
    public function action($action,$parameters = []){
        return $this->route($action,$parameters,app(Router::class)->findRouteByAction($action));
    }
    /**
     * parper route to replace
     * @param Route $route
     * @param array $parameters
     * @return string
     */
    protected function toRoute(Route $route,$parameters = []){
        $parameters = array_merge(request()->route()->getSegments(),$parameters);
        $root = strtr(
            rawurlencode(
                $this->replaceRouteDomain($route,$parameters)
            ),$this->dontEncode);
        $uri = strtr(
            rawurlencode(
                $this->replaceRouteParameters($route->getUri(),$parameters)
            ),$this->dontEncode);
        return ($root.preg_replace('/\/{2,}/','/',$uri));
    }

    /**
     * replace route domain Parameters
     * @param Route $route
     * @param $parameters
     * @return string
     */
    protected function replaceRouteDomain(Route $route,&$parameters){
        return rtrim($this->replaceRouteParameters(($route->getDomain()?:$this->url()),$parameters),'/').'/';
    }

    /**
     * replace route Parameters
     * @param $path
     * @param $parameters
     * @return string
     */
    protected function replaceRouteParameters($path,&$parameters){
        if(count($parameters)){
            $path = preg_replace_callback('/\{(.*?)\??\}/',function ($match) use (&$parameters){
               if(isset($parameters[$match[1]])){
                   $val = $parameters[$match[1]];
                   unset($parameters[$match[1]]);
                   return $val;
               }
               return $match[0];
            },$path);
        }
        return trim(preg_replace('/\{.*?\??\}/','',$path),'/');
    }
    /**
     * clean str to
     * @param $string
     * @return mixed
     */
    public  function cleanToUrl($string){
        // Remove special characters
        $string = preg_replace(["/[^\p{L}\/_|+ -]/ui","/[\/_|+ -]+/"],['','-'],$string);

        // Replace blank space with delimeter
        //$string = preg_replace("/[\/_|+ -]+/", '-', $string);
        // Trim delimiter
        $string =  trim($string,'-');
        return strtolower($string);
    }

    /**
     * @return bool|string
     */
    public function __toString()
    {
        return $this->url();
    }
}