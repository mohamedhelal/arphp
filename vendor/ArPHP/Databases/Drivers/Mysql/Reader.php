<?php
/**
 * -----------------------------------
 * File  : Reader.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */


namespace ArPHP\Databases\Drivers\Mysql;




class Reader extends \ArPHP\Databases\Drivers\Reader
{

    /**
     * free query result
     * @return mixed
     */
    public function free()
    {
        if(is_resource($this->query)) {
            $this->query->closeCursor();
        }
    }

    /**
     * get row
     * @return mixed
     */
    public function fetch()
    {
        return $this->query->fetch();
    }

    /**
     * get all rows
     * @return mixed
     */
    public function fetchAll()
    {
       return $this->query->fetchAll();
    }

    /**
     * get rows counts
     * @return mixed
     */
    public function count()
    {
        return $this->query->rowCount();
    }

    /**
     * get query fields
     * @return mixed
     */
    public function fetchFields()
    {
        $count = $this->query->columnCount();
        $result = [];
        for ($index = 0; $index < $count;$index++){
            $result[] = $this->query->getColumnMeta($index);
        }
        return $result;
    }
}