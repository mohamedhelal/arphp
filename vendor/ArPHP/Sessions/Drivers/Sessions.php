<?php
/**
 * -----------------------------------
 * File  : Sessions.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Sessions\Drivers;


use ArPHP\Sessions\SessionsInterface;
use ArPHP\Encryption\Crypter,ArPHP\Cookie\Cookie,ArPHP\Sessions\SessionManager;
class Sessions implements SessionsInterface
{

    /**
     * @var SessionManager
     */
    protected $parent;

    /**
     * set session setting
     * Sessions constructor.
     * @param SessionManager $sessions
     */
    public function __construct(SessionManager &$sessions)
    {
        $this->parent = &$sessions;
    }

    /**
     * create first session
     * @param $data
     * @return mixed
     */
    public function create($data)
    {
        $this->write($data);
    }

    /**
     * write into session
     * @param $data
     * @return mixed
     */
    public function write($data)
    {
        $name = $this->parent->getName();
        $_SESSION[$name] = Crypter::encrypt(json_encode($data));
        if(isset($data['remember']) && $data['remember'] == 1){
            $this->remember($name,$data,$data['expire']);
        }
    }

    /**
     * get session
     * @return mixed
     */
    public function get()
    {
        $name = $this->parent->getName();
        if (isset($_COOKIE[$name])) {
            return (array)Cookie::get($name);
        }elseif(isset($_SESSION[$name])){
            return (array)json_decode(Crypter::decrypt($_SESSION[$name]));
        }
        return false;
    }

    /**
     * delete session
     * @return mixed
     */
    public function destroy()
    {
        unset($_SESSION[$this->parent->getName()]);
        Cookie::destroy($this->parent->getName(),$this->parent->getPath());
    }

    /**
     * set remember
     * @param $name
     * @param $data
     * @param $expire
     * @return bool
     */
    protected function remember($name,$data,$expire)
    {
        Cookie::set(
            $name,
            $data,
            $expire,
            $this->parent->getPath()
        );
        return true;
    }

    /**
     * Cleanup old sessions
     * @return mixed
     */
    public function gc()
    {
        // TODO: Implement gc() method.
    }
}