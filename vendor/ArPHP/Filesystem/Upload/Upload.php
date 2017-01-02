<?php
/**
 * -----------------------------------
 *  project name framework
 * -----------------------------------
 * File:Upload.php
 * User: mohamed
 */

namespace ArPHP\Filesystem\Upload;


use ArPHP\Filesystem\DirectoryNotFound;
use ArPHP\Support\Implementing\JSAOAble;
use Config;
use Traversable;


class Upload implements JSAOAble
{
    protected $name;
    protected $original;
    protected $type;
    protected $size;
    protected $error;
    protected $tmp_name;
    protected $path;
    protected $full_path;
    protected $uploaded;
    public function __construct($file)
    {
        if (isset($file['name']) && !empty($file['name']) && !is_array($file['name'])) {
            foreach ($file as $key => $value) {
                if ($key == 'name') {
                    $this->original = $value;
                    $value = time(). substr($value,strrpos($value,'.'));
                }
                $this->{$key} = $value;
            }
        }
        $this->uploaded = ($this->error === 0 ? true : false);
    }
    /**
     * check if is uploaded
     * @return bool
     */
    public function uploaded(){
        return $this->uploaded;
    }
    /**
     * get class attributes
     * @return mixed
     */
    public function toArray()
    {
        return array_diff_key(get_object_vars($this),array_fill_keys(array('uploaded','tmp_name','error'),null));
    }

    /**
     * check if type exists
     * @param array $types
     * @return bool
     */
    public function checkType(array $types)
    {
        $mimes = Config::get('mimes');
        $allowed = array();
        foreach ((array)$types as $type) {
            if (isset($mimes[$type])) {
                $allowed = array_merge($allowed, (array)$mimes[$type]);
            }
        }
        return (in_array($this->type, $allowed));
    }
    /**
     * check if image
     * @return int
     */
    public function isImage(){
        return (stripos($this->type,'image/') === 0);
    }
    /**
     * move file to path
     * @param $path
     * @param bool|false $filename
     * @return bool
     * @throws DirectoryNotFound
     */
    public function move($path, $filename = false)
    {
        if (!is_dir($path)) {
            throw new DirectoryNotFound('Directory ' . $path . ' not found');
        }
        $this->name = ($filename == false ? $this->name : (strpos($filename, '.') !== false ? $filename : $filename . strstr($this->original, '.')));
        $this->path = rtrim($path , DS) . DS;
        $this->full_path = $this->path . $this->name;
        if (move_uploaded_file($this->tmp_name, $this->full_path)) {
            return true;
        }
        return false;
    }

    /**
     * get name attributes
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * get original attributes
     * @return mixed
     */
    public function getOriginal()
    {
        return $this->original;
    }


    /**
     * get type attributes
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }


    /**
     * get size attributes
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }


    /**
     * get error attributes
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }


    /**
     * get tmp_name attributes
     * @return mixed
     */
    public function getTmpName()
    {
        return $this->tmp_name;
    }


    /**
     * get path attributes
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }


    /**
     * get full_path attributes
     * @return mixed
     */
    public function getFullPath()
    {
        return $this->full_path;
    }




    /**
     * to print it
     * @return mixed
     */
    public function __toString(){
        return $this->toJson();
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
        return new \ArrayIterator($this->toArray());
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
        // TODO: Implement offsetExists() method.
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
        // TODO: Implement offsetGet() method.
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
        // TODO: Implement offsetSet() method.
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
        // TODO: Implement offsetUnset() method.
    }

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize($this->toArray());
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
        $unserialize = unserialize($serialized);
        foreach ($unserialize as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * change items to object
     * @return mixed
     */
    public function toObject()
    {
       return (object)$this->toArray();
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->toArray());
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
       return $this->toArray();
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
}