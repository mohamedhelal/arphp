<?php
/**
 * -----------------------------------
 * File  : ResourceRegistrar.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Routing;


class ResourceRegistrar
{
    /**
     * ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];
     *  ['POST', 'GET', 'HEAD', 'PUT', 'INSERT', 'UPDATE', 'DELETE', 'SELECT'];
     * @var array
     */
    protected $_default = ['index','create','store','show','edit','update','delete','destroy'];
    /**
     * @var Router
     */
    protected $router;

    /**
     * ResourceRegistrar constructor.
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Registrar resources
     * @param $uri
     * @param $action
     */
    public function registrar($uri,  $action){
        $uri = trim($uri,Router::DELIMITER);
        $url = Router::DELIMITER.$uri;
        $uses = null;
        $name = null;
        $new = [];
        if(is_string($action)){
            $new['uses'] = $action;
            $new['as'] = $uri;
        }
        elseif (is_array($action)){
            $new = $action;
        }
        elseif($action instanceof \Closure) {
            $new['uses'] = $action;
            $new['as'] = $uri;
        }
        if(is_string($new['uses'])){
            $new['uses'] = substr($new['uses'],0,strpos($new['uses'],'@'));
        }
        if(!isset($new['as'])){
            $new['as'] = $uri;
        }
        foreach ($this->_default as $item) {
            $item_action = [];
            $item_url = $url.Router::DELIMITER;
            if(is_string($new['uses'])){
                $item_action['uses'] = $new['uses'].'@'.$item;
            }else{
                $item_action['uses'] = $new['uses'];
            }
            $item_action['as'] = $new['as'].'.'.$item;
            $item_action = array_merge((array)$new,$item_action);
            if(in_array($item,['index','create','show','edit','delete'])) {
                if ($item != 'index') {
                    $item_url = $item_url . $item;
                } elseif (in_array($item, ['show', 'edit', 'delete'])) {
                    $item_url = $url . '{' . $uri . '}' . Router::DELIMITER . $item;
                }
                $methods =   ['GET', 'HEAD'] ;
            }else{
                if($item == 'store') {
                    $methods = ['POST', 'INSERT'];
                }elseif($item == 'update'){
                    $methods = ['PUT',  'UPDATE'];
                }else{
                    $methods = ['DELETE'];
                }
            }
            $this->router->match($methods,$item_url,$item_action);
        }

    }
}