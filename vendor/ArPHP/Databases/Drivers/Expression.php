<?php
/**
 * -----------------------------------
 * File  : Expression.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Databases\Drivers;


class Expression
{
    /**
     * the value
     * @var string
     */
    protected $value;

    /**
     * set the value
     * Expression constructor.
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * get the value
     * @return mixed
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * get the value
     * @return mixed
     */
    public function __toString()
    {
        return $this->get();
    }
}