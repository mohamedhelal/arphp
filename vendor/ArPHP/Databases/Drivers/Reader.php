<?php
/**
 * -----------------------------------
 * File  : Reader.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */


namespace ArPHP\Databases\Drivers;



use ArPHP\Databases\Connection;
use PDOStatement;
use PDO;
abstract class Reader
{
    /**
     * @var PDOStatement
     */
    protected $query;
    /**
     * @var Connection
     */
    protected $connection;
    /**
     * @var int
     */
    protected $fetchMode = PDO::FETCH_OBJ;
    /**
     * set reader details
     * @param Connection $connection
     * @param PDOStatement $PDOStatement
     */
    public function __construct(Connection &$connection, PDOStatement $PDOStatement)
    {
        $this->query = $PDOStatement;
        $this->connection = $connection;
        $this->query->setFetchMode($this->fetchMode);
    }

    /**
     * destroy connection
     */
    public function __destruct()
    {
        if (is_resource($this->query)) {
            $this->free();
        }
    }

    /**
     * free query result
     * @return mixed
     */
    abstract public function free();

    /**
     * get row
     * @return mixed
     */
    abstract public function fetch();

    /**
     * get all rows
     * @return mixed
     */
    abstract public function fetchAll();
    /**
     * get query fields
     * @return mixed
     */
    abstract public function fetchFields();

    /**
     * get rows counts
     * @return mixed
     */
    abstract public function count();
}