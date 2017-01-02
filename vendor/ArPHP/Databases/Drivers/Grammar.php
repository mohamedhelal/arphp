<?php
/**
 * -----------------------------------
 * File  : Grammar.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Databases\Drivers;


use ArPHP\Databases\Connection;
use ArPHP\Databases\Model;
use Closure;

class Grammar
{
    /**
     * all columns
     * @var array
     */
    protected $columns = [];
    /**
     * all tables
     * @var array
     */
    protected $from = [];
    /**
     * all joins
     * @var array
     */
    protected $joins = [];
    /**
     * all conditions
     * @var array
     */
    protected $wheres = [];
    /**
     * all orders
     * @var array
     */
    protected $orders = [];
    /**
     * all group
     * @var array
     */
    protected $groups = [];
    /**
     * all having
     * @var array
     */
    protected $having = [];
    /**
     * all union
     * @var array
     */
    protected $unions = [];
    /**
     * start  from
     * @var bool
     */
    protected $offset = false;
    /**
     * end from
     * @var bool
     */
    protected $limit = false;
    /**
     * DISTINCT select
     * @var bool
     */
    protected $distinct = false;
    /**
     * The current Grammar value bindings.
     *
     * @var array
     */
    protected $bindings = [];
    /**
     * @var Grammar
     */
    protected $parent;
    /**
     * @var Model
     */
    protected $model;
    /**
     * @var string
     */
    protected $statement;

    /**
     * Grammar constructor.
     * @param Model $model
     */
    public function __construct(Model &$model)
    {
        $this->model = $model;
    }

    /**
     * @param Grammar $parent
     * @return $this
     */
    public function setParent(Grammar $parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return array
     */
    public function getBindings()
    {
        if ($this->parent instanceof Grammar || $this->parent instanceof Model) {
            return array_merge($this->parent->getBindings(), $this->bindings);
        }
        return $this->bindings;
    }

    /**
     * @param $field
     * @param $value
     * @return string
     */
    public function setBindings($field, $value)
    {
        if ($this->parent instanceof Grammar || $this->parent instanceof Model) {
            return $this->parent->setBindings($field, $value);
        }
        $bindings = $this->getBindings();
        $field = ":" . str_replace(array(Connection::__PREFIX, '.', '-'), '_', $field);
        if (isset($bindings[$field])) {
            $field = $field . count($bindings);
        }
        $this->bindings[$field] = $value;
        return $field;
    }

    /**
     * @return null|string
     */
    public function getClosureContent()
    {
        if (!empty($this->statement)) {
            return '(' . $this->statement . ')';
        } elseif (count($this->wheres)) {
            return '(' . implode(' ', $this->wheres) . ')';
        } elseif (count($this->having)) {
            return '(' . implode(' ', $this->having) . ')';
        }
        return null;
    }

    /**
     * call Closure
     * @param Closure $closure
     * @return mixed
     */
    public function callClosureContent(Closure $closure)
    {
        $class = get_called_class();
        $Grammar = new $class($this->model);
        $Grammar->setParent($this);
        call_user_func($closure, $Grammar);
        $content = $Grammar->getClosureContent();
        $Grammar = null;
        return $content;
    }

    /**
     * clear Grammar
     */
    public function clear()
    {
        foreach (get_object_vars($this) as $key => $val) {
            if (in_array($key, array('connection', 'from'))) {
                continue;
            }
            $this->{$key} = (is_array($val) ? array() : false);
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_diff_key(get_object_vars($this), array_fill_keys(array('statement'), null));
    }

    /**
     * @param $array
     */
    public function setGrammarItems($array)
    {
        foreach ($array as $item => $value) {
            $this->{$item} = $value;
        }
    }


    /**
     * create distinct Grammar
     * @return $this
     */
    public function distinct()
    {
        $this->distinct = true;
        return $this;
    }

    /**
     * set insert or update value
     * @param $field
     * @param bool|false $value
     * @param bool|false $isField
     * @return $this
     */
    public function setValue($field, $value = false, $isField = false)
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                if (is_array($val)) {
                    foreach ($val as $k => $v) {
                        $this->columns[$key][$k] = $this->setBindings($k, $v);
                    }
                } else {
                    $this->setValue($key, $val, $isField);
                }
            }
        } else {
            if ($isField == false) {
                $value = $this->setBindings($field, $value);
            }
            $this->columns[$field] = $value;
        }
        return $this;
    }


    /**
     * set Grammar tables
     * @param $from
     * @return mixed
     */
    public function from($from)
    {
        if (is_array($from)) {
            foreach ($from as $item) {
                $this->from($item);
            }
        } else {
            if (is_string($from) && !empty($from)) {
                $this->from[] = (strpos($from, Connection::__PREFIX) === 0 ? $from : Connection::__PREFIX . $from);
            } elseif ($from instanceof Closure) {
                $call = $this->callClosureContent($from);
                if (!is_string($call) && !empty($call)) {
                    $this->from[] = $call;
                }
                $call = null;
            }
        }
        return $this;
    }


    /**
     * skip rows
     * @param $skip
     * @return $this
     */
    public function skip($skip)
    {
        $this->offset = $skip;
        return $this;
    }

    /**
     * get numbers of  rows
     * @param $take
     * @return $this
     */
    public function take($take)
    {
        $this->limit = $take;
        return $this;
    }

    /**
     * @param string $select
     * @return $this
     */
    public function select($select = '*')
    {
        if (is_array($select) && !empty($select)) {
            foreach ($select as $item) {
                $this->select($item);
            }
        } elseif (!empty($select) && is_string($select)) {
            $this->columns[] = $select;
        } elseif ($select instanceof Closure) {
            $call = $this->callClosureContent($select);
            if (!is_string($call) && !empty($call)) {
                $this->columns[] = $call;
            }
            $call = null;
        }
        return $this;
    }

    /**
     * @param $field
     * @param string $order
     * @return $this
     */
    public function orderBy($field, $order = 'asc')
    {
        if (is_array($field)) {
            $this->orders[] = implode(' , ', $field) . ' ' . $order;
        } else {
            $this->orders[] = $field . ' ' . $order;
        }
        return $this;
    }

    /**
     * @param $field
     * @param string $order
     * @return $this
     */
    public function groupBy($field, $order = 'asc')
    {
        if (is_array($field)) {
            $this->groups[] = implode(' , ', $field) . ' ' . $order;
        } else {
            $this->groups[] = $field . ' ' . $order;
        }
        return $this;
    }

    /**
     * set join tables
     * @param $table
     * @param $on
     * @param string $inner
     * @return $this
     */
    public function join($table, $on = null, $inner = 'INNER')
    {
        if ($on instanceof Closure) {
            $new = new Join($this);
            call_user_func($on, $new);
            $on = $new->getSubJoin();
        } elseif ($on instanceof Expression) {
            $on = $on->get();
        }
        $on = (!empty($on) ? ' ON ' . $on : null);
        if (is_array($table)) {
            $new = [];
            foreach ($table as $item) {
                if (is_string($item) && !empty($item)) {
                    $new[] = (strpos($item, Connection::__PREFIX) === 0 ? $item : Connection::__PREFIX . $item);
                } elseif ($table instanceof Expression) {
                    $new[] = $table->get();
                }
            }
            $table = implode(' , ', $new);
            $new = null;
        } elseif (is_string($table) && !empty($table)) {
            $new[] = (strpos($table, Connection::__PREFIX) === 0 ? $table : Connection::__PREFIX . $table);
        } elseif ($table instanceof Expression) {
            $table = $table->get();
        }
        $this->joins[] = $inner . ' JOIN ' . $table . ' ' . $on;
        return $this;
    }

    /**
     * set left join
     * @param $table
     * @param null $on
     * @return Grammar
     */
    public function leftJoin($table, $on = null)
    {
        return $this->join($table, $on, 'LEFT');
    }

    /**
     * set right join
     * @param $table
     * @param null $on
     * @return Grammar
     */
    public function rightJoin($table, $on = null)
    {
        return $this->join($table, $on, 'RIGHT');
    }

    /**
     * get conditions
     * @return null|string
     */
    public function getCondition()
    {
        $where = null;
        if (count($this->wheres)) {
            $where .= ' WHERE ' . implode(' ', $this->wheres);
        }
        if (count($this->groups)) {
            $where .= ' GROUP BY  ' . implode(' , ', $this->groups);
            if (count($this->having)) {
                $where .= ' HAVING ' . implode(' ', $this->having);
            }
        }
        if (count($this->orders)) {
            $where .= ' ORDER BY  ' . implode(' , ', $this->orders);
        }

        if ($this->limit != false) {
            $where .= ' LIMIT ';
            if ($this->offset != false) {
                $where .= $this->offset . ',';
            }
            $where .= $this->limit;
        }
        return $where;
    }

    /**
     * @param $field
     * @param $value
     * @param string $comparison
     * @param string $logical
     * @param bool $isField
     * @return $this
     */
    public function where($field, $comparison = '=', $value = false, $logical = 'AND', $isField = false)
    {
        $logical = (count($this->wheres) ? $logical : null);
        $comparison = ($comparison === null ? '=' : $comparison);
        if ($comparison instanceof Closure) {
            $this->wheres[] = $logical . ' ' . call_user_func($comparison, $field, $value, $isField);
        } else {
            if (is_string($field)) {
                if (preg_match('/\s+(IS|LIKE|NOT\s+LIKE|IN|NOT\s+IN|BETWEEN)\s+|(!|>|<|=)/i', $field)) {
                    $this->wheres[] = $logical . ' ' . $field;
                } elseif ($value instanceof Closure) {
                    $this->wheres[] = $logical . ' ' . $field . ' ' . $comparison . ' ' . $this->callClosureContent($value);
                } elseif ($value instanceof Expression) {
                    $this->wheres[] = $logical . ' ' . $field . ' ' . $comparison . ' ' . $value->get();
                } else {

                    if (!preg_match('#^\s*(IS|LIKE|NOT\s+LIKE|IN|NOT\s+IN|BETWEEN|([!=><]+))\s*$#iU', $comparison)) {
                        $value = $comparison;
                        $comparison = '=';
                    }

                    if (!$isField) {
                        $value = $this->setBindings($field, $value);
                    } else {
                        if (strtolower($comparison) == 'is' && is_null($value)) {
                            $comparison = 'IS NULL';
                        }
                    }
                    $this->wheres[] = $logical . ' ' . $field . ' ' . $comparison . ' ' . $value;
                }
            } elseif ($field instanceof Closure) {
                $this->wheres[] = $logical . ' ' . $this->callClosureContent($field);
            } elseif ($field instanceof Expression) {
                $this->wheres[] = $logical . ' ' . $field->get();
            }
        }
        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @param string $comparison
     * @param string $logical
     * @param bool $isField
     * @return $this
     */
    public function having($field, $comparison = '=', $value = false, $logical = 'AND', $isField = false)
    {
        $logical = (count($this->having) ? $logical : null);
        $comparison = (empty($comparison) ? '=' : $comparison);
        if ($comparison instanceof Closure) {
            $this->having[] = $logical . ' ' . call_user_func($comparison, $field, $value, $isField);
        } else {
            if (is_string($field)) {
                if (preg_match('/\s+(IS|LIKE|NOT\s+LIKE|IN|NOT\s+IN|BETWEEN)\s+|(!|>|<|=)/i', $field)) {
                    $this->having[] = $logical . ' ' . $field;
                } elseif ($value instanceof Closure) {
                    $this->having[] = $logical . ' ' . $field . ' ' . $comparison . ' ' . $this->callClosureContent($value);
                } elseif ($value instanceof Expression) {
                    $this->having[] = $logical . ' ' . $field . ' ' . $comparison . ' ' . $value->get();
                } else {
                    if (!preg_match('/^\s*(IS|LIKE|NOT\s+LIKE|IN|NOT\s+IN|BETWEEN|=|!|>|<)\s*/i', $comparison)) {
                        $value = $comparison;
                        $comparison = '=';
                    }
                    if (!$isField) {
                        $value = $this->setBindings($field, $value);
                    }
                    $this->having[] = $logical . ' ' . $field . ' ' . $comparison . ' ' . $value;
                }
            } elseif ($field instanceof Closure) {
                $this->having[] = $logical . ' ' . $this->callClosureContent($field);
            } elseif ($field instanceof Expression) {
                $this->having[] = $logical . ' ' . $field->get();
            }
        }
        return $this;
    }


    /**
     * @param $field
     * @param $value
     * @param string $logical
     * @param bool $isField
     * @param bool $not
     * @return Grammar
     */
    public function whereIn($field, $value, $logical = 'AND', $isField = false, $not = false)
    {
        return $this->where($field, function ($field, $value, $isField) use ($not) {
            if (is_array($value)) {
                if (!$isField) {
                    foreach ($value as $i => $item) {
                        $value[$i] = $this->setBindings($field, $item);
                    }
                }
            } elseif ($value instanceof Closure) {
                $value = (array)$this->callClosureContent($value);
            } elseif ($value instanceof Expression) {
                $value = (array)$value->get();
            }
            return $field . ' ' . ($not == true ? 'NOT' : null) . ' IN (' . implode(',', (array)$value) . ')';
        }, $value, $logical, $isField);
    }

    /**
     * @param $field
     * @param $value
     * @param string $logical
     * @param bool $isField
     * @param bool $not
     * @return Grammar
     */
    public function between($field, $value, $logical = 'AND', $isField = false, $not = false)
    {
        return $this->where($field, function ($field, $value, $isField) use ($not) {
            if (is_array($value)) {
                if (!$isField) {
                    foreach ($value as $i => $item) {
                        $value[$i] = $this->setBindings($field, $item);
                    }
                }
            } elseif ($value instanceof Closure) {
                $value = (array)$this->callClosureContent($value);
            } elseif ($value instanceof Expression) {
                $value = (array)$value->get();
            }
            return $field . ' ' . ($not == true ? 'NOT' : null) . ' BETWEEN ' . implode(' AND ', $value);
        }, $value, $logical, $isField);
    }

    /**
     * @param $field
     * @param $value
     * @param string $logical
     * @param bool $isField
     * @param bool $not
     * @return Grammar
     */
    public function like($field, $value, $logical = 'AND', $isField = false, $not = false)
    {

        return $this->where($field, function ($field, $value, $isField) use ($not) {
            $start = substr($value, 0, 1);
            $end = substr($value, -1);
            $right = null;
            $left = null;
            if (in_array($start, array('%', '_'))) {
                $left = $start;
                $value = substr($value, 1);
            }
            if (in_array($end, array('%', '_'))) {
                $right = $end;
                $value = substr($value, 0, -1);
            }
            if ($left == null && $right == null) {
                $left = '%';
                $right = '%';
            }
            $value = $this->setBindings($field, $left . $value . $right);
            return $field . ' ' . ($not == true ? 'NOT' : null) . ' LIKE ' . $value;
        }, $value, $logical, $isField);
    }

    /**
     * @param $field
     * @param $value
     * @param string $logical
     * @param bool $isField
     * @return Grammar
     */
    public function notLike($field, $value, $logical = 'AND', $isField = false)
    {
        return $this->like($field, $value, $logical, $isField, true);
    }

    public function orNotLike($field, $value, $isField = false)
    {
        return $this->like($field, $value, 'OR', $isField, true);
    }

    public function orLike($field, $value, $isField = false)
    {
        return $this->like($field, $value, 'OR', $isField);
    }


    /**
     * set field value
     * @param $field
     * @param string $comparison
     * @param bool $value
     * @param string $logical
     * @return Grammar
     */
    public function whereField($field, $comparison = '=', $value = false, $logical = 'AND')
    {
        return $this->wheres($field, $comparison, $value, $logical, true);
    }

    public function whereNull($field, $logical = 'AND')
    {
        return $this->wheres($field, 'IS', null, $logical, true);
    }

    public function orWhereField($field, $comparison = '=', $value = false)
    {
        return $this->wheres($field, $comparison, $value, 'OR', true);
    }

    public function orWhere($field, $comparison = '=', $value = false)
    {
        return $this->wheres($field, $comparison, $value, 'OR');
    }

    /**
     * @param $field
     * @param $value
     * @param string $logical
     * @param bool $isField
     * @return Grammar
     */
    public function whereNotIn($field, $value, $logical = 'AND', $isField = false)
    {
        return $this->wheresIn($field, $value, $logical, $isField, true);
    }

    public function orWhereNotIn($field, $value, $isField = false)
    {
        return $this->wheresIn($field, $value, 'OR', $isField, true);
    }

    /**
     * @param $field
     * @param $value
     * @param string $logical
     * @param bool $isField
     * @return Grammar
     */
    public function notBetween($field, $value, $logical = 'AND', $isField = false)
    {
        return $this->between($field, $value, $logical, $isField, true);
    }

    public function orNotBetween($field, $value, $isField = false)
    {
        return $this->between($field, $value, 'OR', $isField, true);
    }

    public function orBetween($field, $value, $isField = false)
    {
        return $this->between($field, $value, 'OR', $isField);
    }

    /**
     * get select sql
     * @return null|string
     */
    public function toSql()
    {
        if (count($this->from) == 0) {
            return null;
        }
        $sql = 'SELECT ' . ($this->distinct == true ? ' DISTINCT ' : null);
        $sql .= ' ' . (count($this->columns) == 0 ? ' * ' : implode(' , ', $this->columns));
        $sql .= ' FROM ' . implode(' , ', $this->from) . ' ' . implode(' ', $this->joins);
        $sql .= ' ' . $this->getCondition();
        $this->statement = $sql;
        $sql = null;
        return $this->statement;
    }

    /**
     * get insert sql
     * @param array $values
     * @return null|string
     */
    public function insertSql($values = array())
    {
        $this->setValue($values);
        if (count($this->from) == 0 || count($this->columns) == 0) {
            return null;
        }
        $sql = 'INSERT INTO ' . implode(' , ', $this->from);
        $fields = array();
        $values = array();
        foreach ($this->columns as $key => $value) {
            if (is_numeric($key)) {
                if (empty($fields)) {
                    $fields = array_keys($value);
                }
                $values[] = '( ' . implode(' , ', array_values($value)) . ' ) ';
            } else {
                $fields = array_keys($this->columns);
                $values[] = '( ' . implode(' , ', array_values($this->columns)) . ' ) ';
                break;
            }
        }

        $sql .= '(' . implode(' , ', $fields) . ' ) ';
        $sql .= ' VALUES ';
        $sql .= implode(',', $values);
        return $sql;
    }

    /**
     * get update sql
     * @param array $values
     * @return null|string
     */
    public function updateSql($values = array())
    {
        $this->setValue($values);
        if (count($this->from) == 0 || count($this->columns) == 0) {
            return null;
        }

        $sql = 'UPDATE ' . implode(' , ', $this->from) . ' SET ';
        $values = array();
        foreach ($this->columns as $key => $value) {
            $values[] = $key . ' = ' . $value;
        }
        $sql .= implode(' , ', $values);
        $sql .= $this->getCondition();
        return $sql;
    }

    /**
     * get delete sql
     * @param array $values
     * @return null|string
     */
    public function deleteSql($values = array())
    {
        if (count($this->from) == 0) {
            return null;
        }
        $sql = 'DELETE ' . implode(' , ', $values) . ' FROM ';
        $sql .= implode(' , ', $this->from);
        $sql .= $this->getCondition();
        return $sql;
    }



}