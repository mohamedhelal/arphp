<?php
/**
 * -----------------------------------
 * File  : Join.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Databases\Drivers;


use Closure;

class Join
{
    protected $on = array();
    /**
     * @var Grammar
     */
    protected $query;

    /**
     * Join constructor.
     * @param Grammar $query
     */
    public function __construct(Grammar &$query)
    {
        $this->query = &$query;
    }

    /**
     * create sub join object
     * @param Closure $closure
     * @return mixed
     */
    public function CreateSubJoin(Closure $closure)
    {
        $query = new static($this->query);
        call_user_func($closure, $query);
        return $query->getSubJoin();
    }

    /**
     * get sub join value
     * @return null|string
     */
    public function getSubJoin()
    {
        if (count($this->on)) {
            return '( ' . implode('  ', $this->on) . ' )';
        }
        return null;
    }

    /**
     * set on
     * @param $field
     * @param bool|false $value
     * @param string $comparison
     * @param string $logical
     * @param bool|false $isField
     * @return $this
     */
    public function on($field, $value = false, $comparison = '=', $logical = 'AND', $isField = false)
    {
        $comparison = ($comparison == false ? '=' : $comparison);
        $logical = (count($this->on) == 0 ? null : ($logical == false ? ' AND ' : $logical));
        if ($comparison instanceof Closure) {
            $comparison = call_user_func($comparison, $this, $field, $value, $logical, $isField);
            if (!empty($comparison)) {
                $this->on[] = $comparison;
            }
        } else {
            if (!empty($field) && preg_match('/\s+(LIKE|NOT\s+LIKE|IN|NOT\s+IN|BETWEEN)\s+|(!|>|<|=)/i', $field)) {
                $this->on[] = $logical . ' ' . $field;
            } elseif (!empty($field) && !empty($value)) {
                if ($value instanceof Closure) {
                    $value = $this->query->callClosureContent($value);
                } else {
                    if ($isField == false) {
                        $value = $this->query->setBindings($field, $value);
                    }
                }
                if (!empty($value)) {
                    $this->on[] = $logical . ' ' . $field . ' ' . $comparison . ' ' . $value;
                }
            } elseif ($field instanceof Closure) {
                $field = $this->CreateSubJoin($field);
                if (!empty($field)) {
                    $this->on[] = $logical . ' ' . $field;
                }
            }
        }
        return $this;
    }

    /**
     * create or on
     * @param $field
     * @param bool|false $value
     * @param string $comparison
     * @param bool|false $isField
     * @return Join
     */
    public function orOn($field, $value = false, $comparison = '=', $isField = false)
    {
        return $this->on($field, $value, $comparison, 'OR', $isField);
    }
}