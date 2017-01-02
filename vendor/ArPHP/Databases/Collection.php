<?php
/**
 * -----------------------------------
 * File  : Collection.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */


namespace ArPHP\Databases;



use ArPHP\Pagination\Pagination;
use ArPHP\Support\Implementing\JSAOAble;
use ArPHP\Support\Repository;


class Collection extends Repository
{
    /**
     * @var Pagination
     */
    protected $pagination;
    /**
     * Collection constructor.
     * @param array $items
     */
    public function __construct(array $items)
    {
        parent::__construct($items);
    }

    /**
     * @param $pagination
     * @return $this
     */
    public function setPagination($pagination)
    {
        $this->pagination = $pagination;
        return $this;
    }
    /**
     * @return Pagination
     */
    public function pagination(){
        return $this->pagination;
    }

    /**
     * @return bool
     */
    public function render(){
        return $this->pagination->getPages();
    }
    /**
     * @return array
     */
    public function toArray()
    {
        if(!$this->isEmpty()){
            $result = [];
            foreach ($this->items as $item) {
                if($item instanceof JSAOAble){
                    $result[] = $item->toArray();
                }
            }
            return $result;
        }
        return [];
    }

    /**
     * @return array
     */
    public function toObject()
    {
        if(!$this->isEmpty()){
            $result = [];
            foreach ($this->items as $item) {
                if($item instanceof JSAOAble){
                    $result[] = $item->toObject();
                }
            }
            return $result;
        }
        return [];
    }

    /**
     * get list
     * @return array
     */
    public function toList(){
        $list = [];
        foreach ($this->toArray() as $item) {
            if(is_array($item)){
                foreach ($item as $key => $val) {
                    $list[] = $val;
                }
            }
        }
        return $list;
    }
}