<?php
/**
 * -----------------------------------
 * File  : Connection.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Databases;


use ArPHP\Databases\Drivers\Expression;
use ArPHP\Databases\Drivers\Reader;

abstract class Connection
{
    /**
     * table prefix
     * @var string
     */
    protected $prefix;
    /**
     * all connections
     * @var array
     */
    protected static $_connections = [];
    /**
     * config data
     * @var array
     */
    protected $config = [];
    /**
     * @var \PDO
     */
    protected $connect;
    /**
     * default connection id
     */
    const __DEFAULT = 0;
    /**
     * table prefix replacement
     */
    const __PREFIX  = '#__';
    /**
     * query counter
     * @var int
     */
    public $queries = 0;
    /**
     * Connection constructor.
     * @param array $config
     * @throws DatabaseException
     */
    public function __construct($config = [])
    {
        try{
            $this->config = $config;
            $this->prefix = $config['prefix'];
            $this->connect();
        }catch (\Exception $exception){
            throw new DatabaseException($exception->getMessage());
        }
    }

    /**
     * @param int $name
     * @param array $config
     * @return Connection
     */
    public static function getConnection($name = Connection::__DEFAULT, $config = []){
        if(array_key_exists($name,static::$_connections)){
            return static::$_connections[$name];
        }
        if(empty($config)){
            $database = config('database');
            $default = $database['default'];
            $config  = $database['drivers'][$default];
            $default = null;$database =null;
        }
        $driver = $config['driver'];
        return static::$_connections[$name] = new $driver($config);
    }

    /**
     * @param $sql
     * @return Expression
     */
    public static function raw($sql){
        return new Expression($sql);
    }

    /**
     * @param $statement
     * @param array $parameters
     * @return mixed
     * @throws DatabaseException
     */
    public function query($statement, $parameters = array()){
        try{
            $statement = str_replace(static::__PREFIX,$this->prefix,$statement);
            $statement = str_replace('`',null,$statement);
            $query = $this->execute($statement, $parameters);
            $start = trim($statement, '( ');
            $start = strtoupper($start);
            if (strpos($start, 'SELECT') === 0 || strpos($start, 'SHOW') === 0) {
                $this->queries++;
                return $this->reader($query);
            } else {
                $rowCount =  $this->rowCount($query);
                $this->freeQuery($query);
                $query = null;
                return $rowCount;
            }
        }catch (\Exception $exception){
            $message = $exception->getMessage() . "</div><div id='query'>" . $statement.'</div>';
            throw new DatabaseException($message);
        }
    }

    /**
     * get all rows
     * @param $statement
     * @param array $parameters
     * @return mixed
     */
    public function select($statement, $parameters = array()){
        $rows =  $this->query($statement,$parameters);
        $data = [];
        if($rows instanceof Reader){
            $data =  $rows->fetchAll();
            $rows->free();
        }
        return $data;
    }
   /**
     * get insert
     * @param $statement
     * @param array $parameters
     * @return mixed
     */
    public function insert($statement, $parameters = array()){
        return  $this->query($statement,$parameters);
    }
    /**
     * get insertGetId
     * @param $statement
     * @param array $parameters
     * @return mixed
     */
    public function insertGetId($statement, $parameters = array()){
        $this->query($statement,$parameters);
        return $this->lastInsertId();
    }
   /**
     * update rows
     * @param $statement
     * @param array $parameters
     * @return mixed
     */
    public function update($statement, $parameters = array()){
        return  $this->query($statement,$parameters);
    }
    /**
     * delete all rows
     * @param $statement
     * @param array $parameters
     * @return mixed
     */
    public function delete($statement, $parameters = array()){
        return  $this->query($statement,$parameters);
    }

    /**
     * close query connection
     * @param $query
     * @return mixed
     */
    abstract protected function freeQuery($query);
    /**
     * connection function
     * @return mixed
     */
    abstract protected function connect();

    /**
     * close connection
     * @return mixed
     */
    abstract protected function free();
    /**
     * escape string value
     * @param $value
     * @return mixed
     */
    abstract public function escape($value);

    /**
     * create new Grammar Object
     * @param $model
     * @return mixed
     */
    abstract public function newGrammar($model);

    /**
     * execute Query
     * @param $statement
     * @param array $parameters
     * @return mixed
     */
    abstract protected function execute($statement, $parameters = array());

    /**
     * get Reader Object
     * @param $query
     * @return mixed
     */
    abstract public function reader($query);

    /**
     * get Last Id Insert
     * @param bool|false $name
     * @return mixed
     */
    abstract public function lastInsertId($name = false);

    /**
     * get Rows Count
     * @param $query
     * @return mixed
     */
    abstract public function rowCount($query);

    /**
     * Checks if inside a transaction
     * @return mixed
     */
    abstract public function inTransaction();

    /**
     * Initiates a transaction
     * @return mixed
     */
    abstract public function beginTransaction();

    /**
     *  Commits a transaction
     * @return mixed
     */
    abstract public function commit();

    /**
     * Rolls back a transaction
     * @return mixed
     */
    abstract public function rollBack();
}