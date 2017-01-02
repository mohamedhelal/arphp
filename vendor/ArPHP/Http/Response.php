<?php
/**
 * -----------------------------------
 * File  : Response.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Http;

/**
 * Class Response
 * @package ArPHP\Http
 */
class Response
{
    /**
     * all header
     * @var array
     */
    protected $headers = [];
    /**
     * content
     * @var string
     */
    protected $content = '';

    /**
     * Request constructor.
     */
    public final function __construct()
    {
        $this->header('Content-Type','text/html; charset=utf-8');
    }

    /**
     * set header
     * @param $name
     * @param $content
     * @return $this
     */
    public function header($name,$content){
        $this->headers[$name] = $content;
        return $this;
    }

    /**
     * set content
     * @param $content
     * @return $this
     */
    public function setContent($content){
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function content(){
        if ($this->content instanceof Response) {
            return $this->content->content();
        }else {
            ob_start();
            foreach ($this->headers as $name => $header) {
                header($name .':'. $header,true);
            }
            echo $this->content;
            return ob_get_clean();
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->content();
    }
}