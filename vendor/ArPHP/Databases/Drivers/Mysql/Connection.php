<?php
/**
 * -----------------------------------
 * File  : Connection.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Databases\Drivers\Mysql;

use PDO;
class Connection extends \ArPHP\Databases\Connection
{

    /**
     * close query connection
     * @param \PDOStatement $query
     * @return mixed
     */
    protected function freeQuery($query)
    {
        $query->closeCursor();
    }

    /**
     * connection function
     * @return mixed
     */
    protected function connect()
    {
        $dsn = "mysql:dbname={$this->config['database']};host={$this->config['host']};charset={$this->config['charset']}";
        $this->connect = new PDO($dsn, $this->config['username'], $this->config['password']);
        $this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->connect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

    }

    /**
     * close connection
     * @return mixed
     */
    protected function free()
    {
        // TODO: Implement free() method.
    }

    /**
     * escape string value
     * @param $value
     * @return mixed
     */
    public function escape($value)
    {
        return $this->connect->quote($value);
    }

    /**
     * create new Query Object
     * @param $model
     * @return Grammar
     */
    public function newGrammar($model)
    {
        return new Grammar($model);
    }

    /**
     * execute Query
     * @param $statement
     * @param array $parameters
     * @return \PDOStatement
     */
    protected function execute($statement, $parameters = array())
    {
        $query = $this->connect->prepare($statement);
        $query->execute($parameters);
        return $query;
    }

    /**
     * get Reader Object
     * @param $query
     * @return mixed
     */
    public function reader($query)
    {
       return new Reader($this,$query);
    }

    /**
     * get Last Id Insert
     * @param bool|false $name
     * @return mixed
     */
    public function lastInsertId($name = false)
    {
        return $this->connect->lastInsertId($name);
    }

    /**
     * get Rows Count
     * @param $query
     * @return mixed
     */
    public function rowCount($query)
    {
        return $query->rowCount();
    }

    /**
     * Checks if inside a transaction
     * @return mixed
     */
    public function inTransaction()
    {
        $this->connect->inTransaction();
    }

    /**
     * Initiates a transaction
     * @return mixed
     */
    public function beginTransaction()
    {
        $this->connect->beginTransaction();
    }

    /**
     *  Commits a transaction
     * @return mixed
     */
    public function commit()
    {
        $this->connect->commit();
    }

    /**
     * Rolls back a transaction
     * @return mixed
     */
    public function rollBack()
    {
        $this->connect->rollBack();
    }
}