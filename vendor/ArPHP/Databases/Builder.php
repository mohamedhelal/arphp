<?php
/**
 * -----------------------------------
 * File  : Builder.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Databases;


use ArPHP\Databases\Drivers\Grammar;
use ArPHP\Exceptions\UndefinedExceptions;
use ArPHP\Pagination\Pagination;
use ArPHP\Support\Macro;

class Builder extends Macro
{
    /**
     * @var Connection
     */
    protected $connection;
    /**
     * @var Grammar
     */
    protected $grammar;
    /**
     * @var Model
     */
    protected $model;

    /**
     * Builder constructor.
     * @param Model $model
     */
    public function __construct(Model &$model)
    {
        $this->model = $model;
        $this->setConnection();
        $this->grammar = $this->connection->newGrammar($this->model);
    }

    /**
     * @param int $name
     * @param array $config
     * @return $this
     */
    public function setConnection($name = Connection::__DEFAULT, $config = [])
    {
        $this->connection = app(Connection::class, [$name, $config]);
        return $this;
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param $name
     * @param $arguments
     * @return Model|bool|mixed
     * @throws UndefinedExceptions
     */
    public function __call($name, $arguments)
    {
        $loaded = false;
        $response = false;
        if (method_exists($this->grammar, $name)) {
            $response = call_user_func_array([$this->grammar, $name], $arguments);
            $loaded = true;
        } elseif (method_exists($this->model, $name)) {
            $response = call_user_func_array([$this->model, $name], $arguments);
            $loaded = true;
        }
        elseif (method_exists($this->model, $scope = ('scope' . ucwords($name)))) {
            $arg = array_merge([$this->model], $arguments);
            $response = (call_user_func_array([$this->model, $scope], $arg)?:$this);
            $loaded = true;
        } elseif (method_exists($this->model, $any = ('any' . ucwords($name)))) {
            $response = (call_user_func_array([&$this->model, $any], $arguments)?:$this);
            $loaded = true;
        }

        if (((is_a($response, Connection::class) || is_a($response, Grammar::class) || is_a($response, Builder::class)))) {
            return $this->model;
        }
        if ($loaded == false) {
            if (array_key_exists($name,static::$macros)){
                array_unshift($arguments,$this->model);
                $macros = static::$macros[$name]->bindTo($this->model);
                $response = call_user_func_array($macros,$arguments);
            }else {
                throw new UndefinedExceptions(' Call to undefined method  ' . get_called_class() . '::' . $name . '()');
            }
        }
        if ($response == false) {
            return $this->model;
        }
        return $response;
    }

    /**
     * get all rows
     * @param array $columns
     * @return Collection
     */
    public function get($columns = [])
    {
        $rows = $this->connection->select($this->grammar->select($columns)->toSql(), $this->grammar->getBindings());
        $this->grammar->clear();
        $items = array_map(function ($row) {
            return $this->model->newModel((array)$row, true);
        }, $rows);
        return $this->model->newCollection($items);
    }

    /**
     * @param array $columns
     * @return mixed|null
     */
    public function first($columns = array())
    {
        return $this->get($columns)->first();
    }

    /**
     * @param array $columns
     * @return mixed|null
     */
    public function firstOrFail($columns = array())
    {
        $first = $this->first($columns);
        if (!($first instanceof Model)) {
            return false;
        }
        return $first;
    }

    /**
     * @param array $columns
     * @return mixed|null
     */
    public function firstOrNew($columns = [])
    {
        $first = $this->firstOrFail($columns);
        if (!$first) {
            return $this->model->newModel();
        }
        return $first;
    }

    /**
     * @param $id
     * @param array $columns
     * @return Collection|mixed|null
     */
    public function find($id, $columns = [])
    {
        if (is_array($id)) {
            $this->grammar->whereIn($this->model->qualifiedKeyName(), $id);
            return $this->get($columns);
        }
        $this->grammar->where($this->model->qualifiedKeyName(), $id);
        return $this->first($columns);
    }

    /**
     * @param $id
     * @param array $columns
     * @return Collection|bool|mixed|null
     */
    public function findOrFail($id, $columns = [])
    {
        $find = $this->find($id, $columns);
        if (!($find instanceof Model) && !($find instanceof Collection)) {
            return false;
        }
        return $find;
    }

    /**
     * @param $id
     * @return bool|mixed|null
     */
    public function findOrNew($id)
    {
        $find = $this->findOrFail($id);
        if (!$find) {
            return $this->model->newModel();
        }
        return $find;
    }

    /**
     * @param $page
     * @param int $per_page
     * @param null $page_url
     * @return mixed
     */
    public function pagination($page, $per_page = 15, $page_url = null)
    {

        $query = $this->grammar->toSql();
        $Count = $this->connection->query($query, $this->grammar->getBindings());
        $rowCount = $Count->count();
        $Count->free();
        $page = ((int)$page == false ? 1 : (int)$page);
        $pagination = Pagination::create($rowCount, $per_page, $page, $page_url);
        $pagination->getDefault();
        $offset = $pagination->getOffset();
        $result = $this->skip($offset)->take($per_page)->get();
        $result->setPagination($pagination);
        return $result;
    }

}
