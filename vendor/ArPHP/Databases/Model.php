<?php
/**
 * -----------------------------------
 * File  : Model.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Databases;





use ArPHP\Exceptions\UndefinedExceptions;
use ArPHP\Support\Implementing\JSAOAble;
use Traversable;

abstract class Model  implements JSAOAble
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table;
    /**
     * model table alias
     * @var string
     */
    protected $alias;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * all update or insert attributes
     * @var array
     */
    protected $attributes = [];
    /**
     * all display selected fields
     * @var array
     */
    protected $original = [];
    /**
     * @var array
     */
    protected $fill     = [];
    /**
     * check if exists or not
     * @var bool
     */
    protected $exists   = false;

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'created_at';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'updated_at';
    /**
     * @var Builder
     */
    protected $builder;
    /**
     * Model constructor.
     * @param array $attributes
     * @param bool $exists
     */
    public function __construct($attributes = [],$exists = false)
    {
        $this->exists = $exists;
        $this->fill($attributes);
        $this->setOriginal($attributes);
        $this->setBuilder();
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
       $this->setAttribute($name,$value);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getAttribute($name);
    }
    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return (array_key_exists($name,$this->attributes)|| array_key_exists($name,$this->original));
    }

    /**
     * @param $name
     */
    public function __unset($name)
    {
        unset($this->attributes[$name], $this->original[$name]);
    }
    /**
     * @param $name
     * @param $arguments
     * @return Model|bool|mixed
     * @throws UndefinedExceptions
     */
    public function __call($name, $arguments)
    {
        $loaded = false;
        if(method_exists($this,$scope = ('scope'.ucfirst($name)))){
            array_unshift($arguments,$this);
            $response = call_user_func_array([$this,$scope],$arguments);
            $loaded = true;
        }
        else{
            $response = call_user_func_array([$this->builder,$name],$arguments);
        }
        if($response !== false && is_a($response,Builder::class)){
            $response = $this;
            $loaded = true;
        }
        if($loaded == false && $response === false){
            throw new UndefinedExceptions(' Call to undefined method  ' . get_called_class() . '::' . $name . '()');
        }
        return $response;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        $query = new static();
        return call_user_func_array([$query,$name],$arguments);
    }

    /**
     * @return $this
     */
    public function setBuilder(){
        $this->builder = new Builder($this);
        $this->builder->from($this->table.($this->alias == null ? null :' AS '.$this->alias));
        return $this;
    }
    /**
     * @return bool
     */
    public function exists(){
        return $this->exists;
    }
    /**
     * @return array
     */
    public function getFill(){
        return $this->fill;
    }
    /**
     * @param $attributes
     * @return $this
     */
    public function fill($attributes){
        if(count($this->fill) == 0){
            $this->attributes = $attributes;
        }else{
            foreach ($this->fill as $item) {
                if(isset($attributes[$item])){
                    $this->attributes[$item] = $attributes[$item];
                }
            }
        }
        return $this;
    }
    /**
     * @param $attributes
     * @return $this
     */
    protected function setOriginal($attributes){
        if($this->exists){
            $this->original = $attributes;
        }
        return $this;
    }

    /**
     * @param $items
     * @return Collection
     */
    public function newCollection($items){
        return new Collection($items);
    }

    /**
     * @param array $attributes
     * @param bool $exists
     * @return static
     */
    public function newModel($attributes = [],$exists = false){
        return new static($attributes,$exists);
    }
    /**
     * @return array
     */
    public function getOriginal()
    {
        return $this->original;
    }
    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
    /**
     * @return bool
     */
    public function isEmpty()
    {
        return (count($this->attributes) == 0 && count($this->original) == 0);
    }
    /**
     * set Attribute data
     * @param $name
     * @param $value
     * @return $this
     */
    public function setAttribute($name,$value){
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * get Attribute data
     * @param $name
     * @return mixed
     * @throws UndefinedExceptions
     */
    public function getAttribute($name){
       if(array_key_exists($name,$this->attributes)){
           return $this->attributes[$name];
       }elseif(array_key_exists($name,$this->original)){
           return $this->original[$name];
       }
        throw new UndefinedExceptions(' Undefined property  ' . get_called_class() . '::' . $name);
    }
    /**
     * @return string
     */
    public function getTable(){
        return Connection::__PREFIX.$this->table ;
    }

    /**
     * @return mixed
     */
    public static function getTableName(){
        return (new static())->getTable();
    }
    /**
     * @return string
     */
    public function getKey()
    {
        return $this->getKeyValue();
    }

    /**
     * @return string
     */
    public function getKeyName(){
        return $this->primaryKey;
    }

    /**
     * get qualified key name
     * @return string
     */
    public function qualifiedKeyName()
    {
        return $this->getTable(). '.' . $this->primaryKey;
    }


    /**
     * @return mixed
     */
    public function getKeyValue()
    {
        return $this->getAttribute($this->primaryKey);
    }

    /**
     * @return array
     */
    public function attributesHaveChanged()
    {
        if ($this->exists()) {
            return array_diff_assoc($this->getAttributes(), $this->getOriginal());
        }
        return $this->getAttributes();
    }

    /**
     * @param $attributes
     * @return mixed
     */
    public static function create($attributes)
    {
        $new = new static;
        $new->fill($attributes);
        if($new->save()){
            return $new->getConnection()->lastInsertId();
        }
        return false;
    }
    /**
     * update this model
     * @param array $attributes
     * @return bool|mixed
     */
    public function save($attributes = [])
    {
        if (!empty($attributes)) {
            $this->fill($attributes);
        }
        $attributes = $this->attributesHaveChanged();
        if (count($attributes)) {
            if($this->exists()) {
                if (!empty($this->getKeyValue())) {
                    $this->where($this->qualifiedKeyName(), $this->getKeyValue());
                    $query = $this->updateSql($attributes);
                }
            }else{
                $query = $this->insertSql($attributes);
            }
            $parameters = $this->getBindings();
            $this->builder->clear();
            if (empty($query)) {
                return false;
            }

            $save = $this->getConnection()->query($query, $parameters);
            if(!$this->exists()) {
                $this->{$this->primaryKey} = $this->getConnection()->lastInsertId();
            }
            return $save;
        }
        return false;
    }

    /**
     * delete this model
     * @return mixed
     */
    public function delete()
    {
        if($this->exists()) {
            $this->builder->where($this->qualifiedKeyName(), $this->getKeyValue());
            $query = $this->builder->deleteSql();
            $parameters = $this->builder->getBindings();
            $this->builder->clear();
            return $this->builder->getConnection()->query($query, $parameters);
        }
    }
    /**
     * @param array $columns
     * @return mixed
     */
    public static function all($columns = ['*']){
        $new = new static();
        return $new->get($columns);
    }

    /**
     * @return mixed
     */
    public static function destroy()
    {
        $args = array_filter(func_get_args());
        if(count($args) && !empty($args)) {
            $new = new static;
            if (count($args) == 1 && is_array($args[0])) {
                $args = $args[0];
            }
            $new->whereIn($new->qualifiedKeyName(), $args);
            $query = $new->deleteSql();
            $parameters = $new->getBindings();
            $new->clear();
            return $new->getConnection()->query($query, $parameters);
        }
        return false;
    }

    /**
     * @param $attributes
     * @return bool|mixed
     */
    public  function update($attributes){
        if($this->exists()) {
            if (!empty($this->getKeyValue())) {
                $this->builder->where($this->qualifiedKeyName(), $this->getKeyValue());
            }
        }
        $query = $this->builder->updateSql($attributes);
        $parameters = $this->builder->getBindings();
        $this->builder->clear();
        if (empty($query)) {
            return false;
        }
        return $this->builder->getConnection()->query($query, $parameters);
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
        if ($this->exists()) {
            return new \ArrayIterator($this->original);
        } else {
            return new \ArrayIterator($this->attributes);
        }
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
        return $this->__isset($offset);
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
        return $this->getAttribute($offset);
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
        $this->setAttribute($offset,$value);
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
        $this->__unset($offset);
    }

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        if($this->exists){
            return serialize($this->toArray());
        }
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
        $this->attributes = unserialize($serialized);
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
     * change items to array
     * @return mixed
     */
    public function toArray()
    {
        if($this->exists){
            return array_merge($this->original,$this->attributes);
        }
        return $this->attributes;
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
        return $this->toJson();
    }

    /**
     * to string for print
     */
    public function __toString()
    {
        return $this->toJson();
    }
}