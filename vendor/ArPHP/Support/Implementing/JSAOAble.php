<?php
/**
 * -----------------------------------
 * File  : JSAOAble.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Support\Implementing;


interface JSAOAble extends \Serializable,\JsonSerializable,\ArrayAccess,\IteratorAggregate
{
    /**
     * change items to object
     * @return mixed
     */
    public function toObject();

    /**
     * change items to array
     * @return mixed
     */
    public function toArray();

    /**
     * change items to json
     * @param int $options
     * @param int $depth
     * @return mixed
     */
    public function toJson($options = 0 , $depth = 512);
}