<?php
/**
 * -----------------------------------
 * File  : Controller.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Http;
use ArPHP\Routing\HttpException;
use ArPHP\Routing\HttpProtection;
use ArPHP\Validation\Validation;

/**
 * Class Controller
 * @package ArPHP\Http
 */
class Controller
{
    /**
     * @var HttpProtection
     */
    protected $HttpProtection;
    /***
     * @param Request $request
     * @return mixed|string
     * @throws HttpException
     */
    public function __callControllerMethod(Request $request){
        $method = $request->route()->getMethod();
        if(!method_exists($this,$method)){
            throw new HttpException();
        }
        return app()->call([$this,$method],$request->route()->getSegments());
    }

    /**
     * validation data
     * @param $data
     * @param $rules
     * @param null $callback
     */
    protected function validation($data,$rules,$callback = null){
        $valid = Validation::create($data,$rules);
        $valid->set_messages(lang('validation'));
        $valid->set_callback($callback);
        if(!$valid->passes()){
            die(redirect()->back()->withInput()->error($valid->errors()->all()));
        }
    }

    /**
     * @param $name
     * @param array $item
     * @return $this
     */
    public function middleware($name,$item = ['all']){
        if(!($this->HttpProtection instanceof HttpProtection)){
            $this->HttpProtection = &app(HttpProtection::class);
        }
        $this->HttpProtection->add($name,$item);
        return $this;
    }

    /**
     * destroy object
     */
    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

    public function __toString()
    {
        return ob_get_clean();
    }
}