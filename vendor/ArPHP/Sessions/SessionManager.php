<?php
/**
 * -----------------------------------
 * File  : SessionManager.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Sessions;


use ArPHP\Support\Arr;

class SessionManager
{
    /**
     * encrypt data
     * @var bool
     */
    protected $encrypt = false;
    /**
     * session name
     * @var string
     */
    protected $name;
    /**
     * session table
     * @var null
     */
    protected $table = null;
    /**
     * Session Driver
     * @var SessionsInterface
     */
    protected $driver;
    /**
     * time to expire session
     * @var int
     */
    protected $expire = 360;
    /**
     * time to expire session for remember
     * @var int
     */
    protected $remember_expire = 6000000;
    /**
     * @var null
     */
    protected $userAgent = false;
    /**
     * @var null
     */
    protected $ipAddress = false;
    /**
     * user data
     * @var array
     */
    protected $data = array();
    /**
     * flash data
     * @var array
     */
    protected $flash = array();
    /**
     * cookie path
     * @var string
     */
    protected $path = '/';
    /**
     * cookie domain
     * @var bool
     */
    protected $domain = false;
    /**
     * secure the cookie
     * @var bool
     */
    protected $secure = false;
    /**
     * flash session name
     */
    const FLASH_NAME = 'FlashData';
    /**
     * flash prefix name
     */
    const FLASH_KEY = 'FLASH';
    /**
     * flash new name
     */
    const FLASH_NEW = ':NEW:';
    /**
     * flash old name
     */
    const FLASH_OLD = ':OLD:';
    /**
     * protection token
     */
    const TOKEN = 'SECURE_TOKEN';
    /**
     * @var string
     */
    protected $flash_name;
    /**
     * set session setting
     * @param array $config
     */
    public function __construct($config = array())
    {
        if(!session_id()){
            ob_start();
            session_start();
        }
        $default = config('session');
        $config = array_merge($default, $config);
        $this->init($config);
        $this->loadDriver();
        $this->flash_name = $this->name.static::FLASH_NAME;
        if ($this->read()) {
            $this->update();
        } else {
            $this->create();
        }
        if (!$this->has(static::TOKEN)) {
            $this->set(static::TOKEN, md5(time()));
        }
        $this->gc();

    }

    /**
     * get token value
     * @return mixed
     */
    public function token()
    {
        return $this->get(static::TOKEN);
    }

    /**
     * set session values
     * @param $config
     */
    protected function init($config)
    {
        foreach (get_object_vars($this) as $key => $value) {
            if (in_array($key, array('data', 'flash'))) {
                continue;
            }
            if (isset($config[$key])) {
                $this->{$key} = $config[$key];
            }
        }

    }

    /**
     * load driver
     * @return SessionsInterface
     */
    protected function loadDriver()
    {
        $this->driver = new $this->driver($this);
        return $this->driver;
    }

    /**
     * get session id
     * @return string
     */
    public function session_id()
    {
        if (isset($this->data['session_id'])) {
            return $this->data['session_id'];
        }
        return sha1(uniqid(time()));
    }

    /**
     * get user ip address
     * @return mixed
     */
    public function ipAddress()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * get user browser
     * @return mixed
     */
    public function user_agent()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * create session
     */
    protected function create()
    {
        $this->data = array(
            'session_id' => $this->session_id(),
            'user_agent' => $this->user_agent(),
            'ip_address' => $this->ipAddress(),
            'expire' => (time() + (int)$this->expire),
            'remember' => 0
        );
        $this->driver->create($this->data);
    }

    /**
     * read session data
     * @return bool
     */
    protected function read()
    {
        $session = $this->driver->get();
        if (!is_array($session) || empty($session) || !isset($session['expire'])) {
            return false;
        }

        $this->data = $session;
        $session = null;
        if (isset($_SESSION[$this->flash_name])) {
            $this->flash = $_SESSION[$this->flash_name];
        }
        return true;
    }

    /**
     * update session data
     * @param bool|false $update
     * @return bool
     */
    protected function update($update = false)
    {
        $expire = $this->data['expire'];
        if ($expire > (time() - $this->expire) && $update == false) {
            return true;
        }
        $expire = (time() + (int)$this->expire);
        $remember = (isset($this->data['remember']) && $this->data['remember'] == 1 ? 1 : 0);
        if ($remember) {
            $expire = (time() + $this->remember_expire);
        }
        $data = array(
            'session_id' => $this->session_id(),
            'user_agent' => $this->user_agent(),
            'ip_address' => $this->ipAddress(),
            'expire' => $expire,
            'remember' => $remember
        );
        $this->data = array_merge($this->data, $data);
        $this->driver->write($this->data);
    }


    /**
     * set key and values
     * @param $key
     * @param bool|false $value
     * @return $this
     */
    public function set($key, $value = false)
    {
        if (!is_array($key)) {
            $key = array($key => $value);
        }
        $this->data = array_merge($this->data, $key);
        $this->update(true);
        return $this;
    }

    /**
     * set flash
     * @param $key
     * @param bool|false $value
     * @return $this
     */
    public function flash($key, $value = false)
    {
        if (!is_array($key)) {
            $key = array($key => $value);
        }
        $new = static::FLASH_KEY . static::FLASH_NEW;
        foreach ($key as $k => $v) {
            $this->flash[$new . $k] = $v;
        }
        $this->updateFlash();

        return $this;
    }

    /**
     * check if has
     * @param $key
     * @return mixed
     */
    public function has($key)
    {
        $result = Arr::get($this->data, $key);
        if ($result === false) {
            $key = static::FLASH_KEY . static::FLASH_OLD . $key;
            return Arr::get($this->flash, $key);
        }
        return $result;
    }

    /**
     * get item from array
     * @param $key
     * @param bool|false $default
     * @return mixed
     */
    public function get($key, $default = false)
    {
        $result = Arr::get($this->data, $key, $default);
        if ($result == false) {
            $key = static::FLASH_KEY . static::FLASH_OLD . $key;
            return Arr::get($this->flash, $key, $default);
        }
        return $result;
    }

    /**
     * keep flash data
     * @param $keys
     * @return $this
     */
    public function keep($keys)
    {
        $update = false;
        $new = static::FLASH_KEY . static::FLASH_NEW;
        $old = static::FLASH_KEY . static::FLASH_OLD;
        foreach ((array)$keys as $key) {
            $flashKey = $old . $key;
            if (isset($this->flash[$flashKey])) {
                $newKey = str_replace($old, $new, $flashKey);
                $this->flash[$newKey] = $this->flash[$flashKey];
                unset($this->flash[$flashKey]);
                $update = true;
            }
        }
        if ($update == true) {
            $this->updateFlash();
        }
        return $this;
    }

    /**
     * update flash data
     */
    protected function updateFlash()
    {
        $this->flash = $_SESSION[$this->flash_name] = $this->flash;
    }

    /**
     * read flash data
     */
    protected function flashRefresh()
    {
        $new = static::FLASH_KEY . static::FLASH_NEW;
        $old = static::FLASH_KEY . static::FLASH_OLD;
        foreach ($this->flash as $key => $value) {
            if (strpos($key, $old) === 0) {
                unset($this->flash[$key]);
            } elseif (strpos($key, $new) === 0) {
                unset($this->flash[$key]);
                $new_key = str_replace($new, $old, $key);
                $this->flash[$new_key] = $value;
            }
        }
        $this->updateFlash();

    }

    /**
     * Cleanup old sessions
     * @return mixed
     */
    public function gc()
    {
        $this->flashRefresh();
        $this->driver->gc();
    }

    /**
     * delete session
     * @return mixed
     */
    public function destroy()
    {
        $this->driver->destroy();
    }

    /**
     * get all session
     * @return array
     */
    public function all()
    {
        $data = array(
            'session_id',
            'user_agent',
            'ip_address',
            'expire',
            'remember',
            static::TOKEN
        );
        $data = array_fill_keys($data, null);
        return array_filter(array_diff_key($this->data, $data));
    }

    /**
     * set remember
     */
    public function remember()
    {
        $this->data['remember'] = 1;
        $this->update(true);
        return $this;
    }

    /**
     * get all data
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * get encrypt
     * @return string
     */
    public function getEncrypt()
    {
        return $this->encrypt;
    }

    /**
     * get name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * get expire
     * @return string
     */
    public function getExpire()
    {
        return $this->expire;
    }

    /**
     * get remember expire time
     * @return int
     */
    public function getRememberExpire()
    {
        return $this->remember_expire;
    }

    /**
     * get path
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * get domain
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * get secure
     * @return string
     */
    public function getSecure()
    {
        return $this->secure;
    }

    /**
     * get table name
     * @return null
     */
    public function getTable()
    {
        return $this->table;
    }

}