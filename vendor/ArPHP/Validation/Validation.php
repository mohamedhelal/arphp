<?php
/**
 * -----------------------------------
 *  project name framework
 * -----------------------------------
 * File:Validation.php
 * User: mohamed
 */
namespace ArPHP\Validation;

use Arr;

/*
*----------------------------------------
* Class Name  Validation 
*----------------------------------------
*/

class Validation
{
    protected $_data = array();
    protected $_labels = array();
    protected $_rules = array();
    protected $errors = array();
    protected $messages = array();
    protected $callback = false;
    protected static $default_messages = array(
        'required' => 'this field :field is required',
        'min' => 'this field :field must be Greater than or equal  :min',
        'max' => 'this field :field must be Less than  or equal  :max',
        'email' => 'this field :field must be email like exmple (exmple@exmple.com)',
        'url' => 'this field :field must be url like exmple (http://exmple.com)',
        'phone' => 'this field :field must be phone number and Greater than 11 number',
        'number' => 'this field :field must be number',
        'match' => 'this field :field must be match :match',
        'confirm' => 'this field :field must be the same  :confirm',
        'unique' => 'this field :field " :data " ready exists'
    );

    /**
     * set default data and fields
     * @param array $data
     * @param array $rules
     */
    public function __construct(array $data, array $rules = array())
    {
        if (count($data) == 0) {
            return;
        }
        $this->_data = $data;
        foreach ($rules as $field => $value) {
            $this->set_rule($field,$value['label'],$value['rule']);
        }
    }

    /**
     * create new object
     * @param array $data
     * @param array $rules
     * @return Validation
     */
    public static function create(array $data, array $rules = array())
    {
        return new self($data, $rules);
    }

    /**
     * set messages
     * @param $messages
     * @return $this
     */
    public function set_messages($messages)
    {
        $this->messages = (array)$messages;
        return $this;
    }

    /**
     * @param $type
     * @param $message
     * @return $this
     */
    public function set_message($type, $message)
    {
        $this->messages[$type] = $message;
        return $this;
    }


    /**
     * set field rule
     * @param $field
     * @param $label
     * @param $rule
     * @return $this
     */
    public function set_rule($field, $label, $rule)
    {
        $this->_labels[$field] = $label;
        $this->_rules[$field] = $rule;
        return $this;
    }

    /**
     * set callback function
     * @param $callback
     * @return $this
     */
    public function set_callback($callback)
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * set fields error
     * @param $field
     * @param $error_name
     * @param $replace
     * @return bool
     */
    protected function set_error($field, $error_name, $replace)
    {
        $error = (isset($this->messages[$error_name]) ? $this->messages[$error_name] :
            (isset(static::$default_messages[$error_name]) ? static::$default_messages[$error_name] : null));
        if (is_null($error)) {
            return false;
        }
        $error = str_replace(array_keys($replace), array_values($replace), $error);
        $this->errors[$field] = $error;
        return false;
    }

    /**
     * check if fails
     * @return array|bool
     */
    public function fails()
    {
        foreach ($this->_rules as $field => $rules) {
            if (array_key_exists($field, $this->errors)) {
                continue;
            }
            if(!is_array($rules)){
                $rules = explode('|',$rules);
            }
            foreach ($rules as $rule) {
                if (method_exists($this, $rule) && (call_user_func(array($this, $rule), $field)) == false) {
                    continue;
                } elseif (strstr($rule, ':')) {
                    $explode = explode(':', $rule, 2);
                    if (method_exists($this, $explode[0]) && (call_user_func(array($this, $explode[0]), $field, $explode[1])) == false) {
                        continue;
                    }
                }
            }
        }
        return $this->errors();
    }

    /**
     * if passes
     * @return bool
     */
    public function passes()
    {
        return ($this->fails() == false ? true : false);
    }

    /**
     * get errors class
     * @return Messages|bool
     */
    public function errors()
    {
        return (count($this->errors) ? new Messages($this->errors) : false);
    }

    /**
     * get field data
     * @param $field
     * @return bool
     */
    protected function get_field_data($field)
    {
        $field = str_replace(array('][', '[', ']'), array('.', '.', null), $field);
        return Arr::get($this->_data, $field);
    }

    /**
     * check field data
     * @param $field
     * @return bool
     */
    protected function required($field)
    {
        $data = $this->get_field_data($field);
        $data = (is_array($data) ? (empty($data) || count(array_filter($data)) == 0 ? null : true) : strlen(trim($data)));

        if (empty($data) ) {
            return $this->set_error($field, 'required', array(':field' => $this->_labels[$field]));
        }
        return true;
    }

    /**
     * match data
     * @param $field
     * @param $match
     * @return bool
     */
    protected function match($field, $match)
    {
        $data = trim($this->get_field_data($field));
        if (preg_match('#^' . $match . '$#', $data) == false) {
            return $this->set_error($field, 'match', array(':field' => $this->_labels[$field], ':match' => $match));
        }
        return true;
    }

    /**
     * check if text bager than len
     * @param $field
     * @param $len
     * @return bool
     */
    protected function min($field, $len)
    {
        $data = strlen(trim($this->get_field_data($field)));
        $len = ((int)$len);
        if ($len > $data) {
            return $this->set_error($field, 'min', array(':field' => $this->_labels[$field], ':min' => $len));
        }
        return true;
    }

    /**
     * check if text small than len
     * @param $field
     * @param $len
     * @return bool
     */
    protected function max($field, $len)
    {
        $data = strlen(trim($this->get_field_data($field)));
        $len = ((int)$len);
        if ($len < $data) {
            return $this->set_error($field, 'max', array(':field' => $this->_labels[$field], ':max' => $len));
        }
        return true;
    }

    /**
     * check if text is email
     * @param $field
     * @return bool
     */

    protected function email($field)
    {
        $data = trim($this->get_field_data($field));
        if (filter_var($data, FILTER_VALIDATE_EMAIL) == false) {
            return $this->set_error($field, 'email', array(':field' => $this->_labels[$field]));
        }
        return true;
    }

    /**
     * check if text is url
     * @param $field
     * @return bool
     */
    protected function url($field)
    {
        $data = trim($this->get_field_data($field));
        if (filter_var($data, FILTER_VALIDATE_URL) == false) {
            return $this->set_error($field, 'url', array(':field' => $this->_labels[$field]));
        }
        return true;
    }

    /**
     * check if text is number int or float
     * @param $field
     * @return bool
     */
    protected function number($field)
    {
        $data = trim($this->get_field_data($field));
        if (filter_var($data, FILTER_VALIDATE_INT) == false && filter_var($data, FILTER_VALIDATE_FLOAT) == false) {
            return $this->set_error($field, 'number', array(':field' => $this->_labels[$field]));
        }
        return true;
    }

    /**
     * check if text is phone number
     * @param $field
     * @return bool
     */
    protected function phone($field)
    {
        $data = trim($this->get_field_data($field));
        if (preg_match('/^[0-9]{11,}$/', $data) == false) {
            return $this->set_error($field, 'phone', array(':field' => $this->_labels[$field]));
        }
        return true;
    }

    /**
     * confirm data
     * @param $field
     * @param $field2
     * @return bool
     */
    protected function confirm($field, $field2)
    {
        $data = trim($this->get_field_data($field));
        $data2 = trim($this->get_field_data($field2));
        if ($data != $data2) {
            return $this->set_error($field, 'confirm', array(':field' => $this->_labels[$field], ':confirm' => $this->_labels[$field2]));
        }
    }

    /**
     * check if data its unique
     * @param $field
     * @param bool $callback
     * @return bool
     */
    protected function unique($field, $callback = false)
    {
        $data = trim($this->get_field_data($field));
        $callback = ($callback != false && is_callable($callback) ? $callback : $this->callback);
        if ($callback != false && is_callable($callback) && call_user_func($callback, $field, $data) == false) {
            return $this->set_error($field, 'unique', array(':field' => $this->_labels[$field], ':data' => $data));
        }
        return true;
    }
}