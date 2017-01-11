<?php
/**
 * -----------------------------------
 * File  : Repository.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Support;



use ArPHP\Sessions\SessionManager;
use ArPHP\Support\Implementing\JSAOAble;
use Traversable;

class Repository extends Macro implements JSAOAble
{
    /**
     * All of the configuration items.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Create a new configuration repository.
     *
     * @param  array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param  string $key
     * @return bool
     */
    public function has($key)
    {
        return Arr::has($this->items, $key);
    }

    /**
     * Get the specified configuration value.
     *
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->items, $key, $default);
    }

    /**
     * Set a given configuration value.
     *
     * @param  array|string $key
     * @param  mixed $value
     * @return void
     */
    public function set($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $innerKey => $innerValue) {
                Arr::set($this->items, $innerKey, $innerValue);
            }
        } else {
            Arr::set($this->items, $key, $value);
        }
    }

    /**
     * Prepend a value onto an array configuration value.
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function prepend($key, $value)
    {
        $array = $this->get($key);

        array_unshift($array, $value);

        $this->set($key, $array);
    }

    /**
     * Push a value onto an array configuration value.
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function push($key, $value)
    {
        $array = $this->get($key);

        $array[] = $value;

        $this->set($key, $array);
    }

    /**
     * Get all of the configuration items for the application.
     *
     * @return array
     */
    public function all()
    {
        return array_diff_key($this->items,array_fill_keys([__USER_METHOD__,SessionManager::TOKEN],null));
    }

    /**
     * @param bool $key
     * @param string $delimiter
     * @return $this
     */
    public function remove($key = false, $delimiter = '.')
    {
        Arr::remove($this->items, $key, $delimiter);
        return $this;
    }

    /**
     * get first match
     * @param callable|null $callback
     * @param null $default
     * @return mixed|null
     */
    public function first(callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            return count($this->items) > 0 ? reset($this->items) : null;
        }

        return Arr::first($this->items, $callback, $default);
    }

    /**
     * @param callable $callback
     * @return $this
     */
    public function each(callable $callback)
    {
        foreach ($this->all() as $key => $item) {
            if ($callback($item, $key) === false) {
                break;
            }
        }
        return $this;
    }

    /**
     * is empty
     * @return bool
     */
    public function isEmpty()
    {
        return (count($this->all()) == 0);
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset,$value);
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize($this->all());
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        $this->items = unserialize($serialized);
    }

    /**
     * change items to object
     * @return mixed
     */
    public function toObject()
    {
        return (object)$this->all();
    }

    /**
     * change items to array
     * @return mixed
     */
    public function toArray()
    {
        return (array)$this->all();
    }

    /**
     * change items to json
     * @param int $options
     * @param int $depth
     * @return mixed
     */
    public function toJson($options = 0, $depth = 512)
    {
        return json_encode($this->toArray(),$options,$depth);
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return $this->all();
    }
}