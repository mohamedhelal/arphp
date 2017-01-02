<?php
/**
 * -----------------------------------
 * File  : Arr.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Support;


class Arr
{
    /**
     * set value to array
     * @param $array
     * @param $key
     * @param $val
     * @param string $delimiter
     * @return mixed
     */
    public static function set(&$array, $key, $val, $delimiter = '.')
    {
        if (strstr($key, $delimiter)) {
            $explode = explode($delimiter, trim($key, $delimiter . ' '), 2);
            if (!isset($array[$explode[0]])) {
                $array[$explode[0]] = array();
            }
            return static::set($array[$explode[0]], $explode[1], $val, $delimiter);
        } else {
            $array[$key] = $val;
        }
        return $array;
    }

    /**
     * get value from array
     * @param $array
     * @param bool $key
     * @param bool $default
     * @param string $delimiter
     * @return bool
     */
    public static function get($array, $key = false, $default = false, $delimiter = '.')
    {
        if ($key === false) {
            return $array;
        } elseif (isset($array[$key])) {
            return $array[$key];
        } elseif (strpos($key, $delimiter) != false) {
            $split = explode($delimiter, trim($key, ' ' . $delimiter), 2);
            if (!isset($array[$split[0]])) {
                return $default;
            }
            return static::get($array[$split[0]], (isset($split[1]) ? $split[1] : false), $default, $delimiter);

        }
        return $default;
    }

    /**
     * check if array has key or not
     * @param $array
     * @param $key
     * @param string $delimiter
     * @return bool
     */
    public static function has($array, $key, $delimiter = '.')
    {
        return (static::get($array, $key, false, $delimiter) !== false);
    }

    /**
     * get first item
     * @param $array
     * @param \Closure $callback
     * @param null $default
     * @return null
     */
    public static function first($array, \Closure $callback, $default = null)
    {
        foreach ($array as $key => $value) {
            if (call_user_func($callback, $key, $value)) {
                return $value;
            }
        }
        return $default;
    }

    /**
     * remove item from array
     * @param $array
     * @param $key
     * @param string $delimiter
     * @return mixed
     */
    public static function remove($array, $key, $delimiter = '.')
    {
        if (strstr($key, $delimiter)) {
            $split = explode($delimiter, trim($key, ' ' . $delimiter), 2);
            if (!isset($array[$split[0]])) {
                return $array;
            }
            return static::remove($array[$split[0]], $split[1], $delimiter);
        } elseif (isset($array[$key])) {
            unset($array[$key]);
        }
        return $array;
    }

    /**
     * create tree from array
     * @param  $array
     * @param int $id
     * @param $primary
     * @param $parent
     * @param int $counter
     * @param array $tree
     * @return array
     */
    public static function tree($array, $id = 0, $primary, $parent, $counter = 0, &$tree = array())
    {
        ++$counter;
        foreach ($array as $item) {
            $type = gettype($item);
            $item = (object)$item;
            if (((int)$item->{$parent}) === (int)$id) {
                $item->counter = $counter;
                $primary_id = $item->{$primary};
                settype($item, $type);
                array_push($tree, $item);
                $tree = static::tree($array, $primary_id, $primary, $parent, $counter, $tree);
            }
        }
        return $tree;
    }

    /**
     * @param $array
     * @param int $id
     * @param $primary
     * @param $parent
     * @param array $tree
     * @return array
     */
    public static function parents($array, $id = 0, $primary, $parent, &$tree = array())
    {
        foreach ($array as $item) {
            $type = gettype($item);
            $item = (object)$item;
            if (((int)$item->{$primary}) === (int)$id) {
                $primary_id = $item->{$parent};
                settype($item, $type);
                array_unshift($tree, $item);
                $tree = static::parents($array, $primary_id, $primary, $parent, $tree);
            }
        }
        return $tree;
    }
}