<?php
/**
 *#--------------------------------
 * Project name phpframe
 *#--------------------------------
 * Created by mohamed.
 * File name  Form.php
 * Date       21/07/15
 */

namespace ArPHP\Html;



use ArPHP\Exceptions\UndefinedExceptions;
use ArPHP\Sessions\SessionManager;
use ArPHP\Support\Implementing\JSAOAble;
use Arr;
use ArPHP\Support\Macro;
use Str;
use ArPHP\Html\HtmlCollection as HtmlC;

class Form extends Macro
{
    /**
     * form data
     * @var array
     */
    protected $form_data = array();

    /**
     * attrs by type
     * @var array
     */
    protected $attrs = array();

    /**
     * set type attrs
     * @param $attrs
     */
    public function attrs($attrs)
    {
        $this->attrs = array_merge($this->attrs, (array)$attrs);
    }

    /**
     * to call macro functions
     * @param $name
     * @param $args
     * @return bool|mixed
     * @throws UndefinedExceptions
     */
    public function __call($name, $args)
    {
        if ($result = $this->getMacro($name, $args)) {
            return $result;
        }
        throw new UndefinedExceptions("class '" . get_called_class() . "' does not have a method '$name'");
    }

    /**
     * set form data
     * @param $data
     */
    public function model($data)
    {
        if ($data instanceof JSAOAble) {
            $data = $data->toArray();
        }
        $this->form_data = (array)$data;
    }

    /**
     * get form field value
     * @param $name
     * @param bool $default
     * @return mixed
     */
    protected function getFieldValue($name, $default = false)
    {
        $name = str_replace(array('[', '][', ']'), array('.', '.', ''), $name);
        return Arr::get($this->form_data, $name, $default);
    }

    /**
     * check if field data exists
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return ($this->getFieldValue($name) != false);
    }

    /**
     * get field value
     * @param $name
     * @param bool $default
     * @return mixed
     */
    public function get($name, $default = false)
    {
        return $this->getFieldValue($name, $default);
    }

    /**
     * prepare input attr
     * @param $name
     * @param bool|false $value
     * @param array $attr
     * @return array
     */
    public function prepare($name, $value = false, $attr = array())
    {
        $attr = (array)$attr;
        if (!is_array($name)) {
            $attr['name'] = $name;
            $attr['value'] = $value;
        } else {
            $attr = array_merge($attr, $name);
        }
        if ($attr['type'] != 'password' && (!isset($attr['value']) || $value === false)) {
            $attr['value'] = $this->getFieldValue($attr['name']);
        }
        if ($attr['type'] == 'file') {
            unset($attr['value']);
        }
        if (isset($this->attrs[$attr['type']])) {
            foreach ((array)$this->attrs[$attr['type']] as $key => $val) {
                if (isset($attr[$key])) {
                    $attr[$key] = $attr[$key] . ' ' . $val;
                } else {
                    $attr[$key] = $val;
                }
            }

        }
        return $attr;
    }

    /**
     * form open
     * @param array $attr
     * @return string
     */
    public function open($attr = array())
    {
        if (isset($attr['route'])) {
            $attr['action'] = call_user_func_array('route', (array)$attr['route']);
            unset($attr['route']);
        } elseif (isset($attr['url'])) {
            $attr['action'] = $attr['url'];
            unset($attr['url']);
        } elseif (isset($attr['action'])) {
            $attr['action'] = call_user_func_array('action', (array)$attr['action']);
        }


        if (!isset($attr['method'])) {
            $attr['method'] = 'POST';
        }
        if (isset($attr['file'])) {
            unset($attr['file']);
            $attr['enctype'] = 'multipart/form-data';
        }
        $hidden = null;
        if (!in_array(strtolower($attr['method']), array('post', 'get'))) {
            $method = strtoupper($attr['method']);
            $attr['method'] = 'POST';
            $hidden = $this->hidden(__USER_METHOD__, $method);
        }
        return '<form ' . HtmlC::attr($attr) . " >\n" . $hidden . "\n";
    }

    /**
     * close form
     * @return string
     */
    public function close()
    {
        return ($this->Token() . "\n</form>");
    }

    /**
     * create token input
     * @return string
     */
    public function Token()
    {
        return $this->hidden(SessionManager::TOKEN, \Session::token());
    }

    /**
     * form label
     * @param $for
     * @param $value
     * @param array $attr
     * @return string
     */
    public function label($for, $value, $attr = array())
    {
        $attr = (array)$attr;
        $attr['for'] = $for;
        return '<label ' . HtmlC::attr($attr) . '>' . $value . '</label>';
    }

    /**
     * create input field
     * @param $name
     * @param bool|false $value
     * @param array $attr
     * @return string
     */
    public function input($name, $value = false, $attr = array())
    {
        if (!isset($attr['type'])) {
            $attr['type'] = 'text';
        }
        $attr = $this->prepare($name, $value, $attr);
        if (!isset($attr['id'])) {
            $attr['id'] = $name;
        }
        return '<input ' . HtmlC::attr($attr) . '/>';
    }

    /**
     * create textarea input
     * @param $name
     * @param bool|false $value
     * @param array $attr
     * @return string
     */
    public function textarea($name, $value = false, $attr = array())
    {
        $attr = (array)$attr;
        $attr['type'] = 'textarea';
        $attr = $this->prepare($name, $value, $attr);
        $value = $attr['value'];
        unset($attr['type'], $attr['value']);
        return '<textarea ' . HtmlC::attr($attr) . '>' . $value . '</textarea>';
    }

    /**
     * create text field
     * @param $name
     * @param bool|false $value
     * @param array $attr
     * @return string
     */
    public function text($name, $value = false, $attr = array())
    {
        return $this->input($name, $value, array_merge(array('type' => 'text'), (array)$attr));
    }

    /**
     * create hidden field
     * @param $name
     * @param bool|false $value
     * @param array $attr
     * @return string
     */
    public function hidden($name, $value = false, $attr = array())
    {
        return $this->input($name, $value, array_merge(array('type' => 'hidden'), (array)$attr));
    }

    /**
     * create password field
     * @param $name
     * @param bool|false $value
     * @param array $attr
     * @return string
     */
    public function password($name, $value = false, $attr = array())
    {
        return $this->input($name, $value, array_merge(array('type' => 'password'), (array)$attr));
    }

    /**
     * create file field
     * @param $name
     * @param array $attr
     * @return string
     */
    public function file($name, $attr = array())
    {
        return $this->input($name, null, array_merge(array('type' => 'file'), (array)$attr));
    }

    /**
     * create image field
     * @param $name
     * @param array $attr
     * @return string
     */
    public function image($name, $attr = array())
    {
        return $this->input($name, null, array_merge(array('type' => 'image'), (array)$attr));
    }

    /**
     * create email field
     * @param $name
     * @param bool|false $value
     * @param array $attr
     * @return string
     */
    public function email($name, $value = false, $attr = array())
    {
        return $this->input($name, $value, array_merge(array('type' => 'email'), (array)$attr));
    }

    /**
     * create url field
     * @param $name
     * @param bool|false $value
     * @param array $attr
     * @return string
     */
    public function url($name, $value = false, $attr = array())
    {
        return $this->input($name, $value, array_merge(array('type' => 'url'), (array)$attr));
    }

    /**
     * create number field
     * @param $name
     * @param bool|false $value
     * @param array $attr
     * @return string
     */
    public function number($name, $value = false, $attr = array())
    {
        return $this->input($name, $value, array_merge(array('type' => 'number'), (array)$attr));
    }

    /**
     * creater for check boxs
     * @param $name
     * @param bool|false $value
     * @param bool|false $checked
     * @param array $attr
     * @return string
     */
    public function createCheckBox($name, $value = false, $checked = false, $attr = array())
    {
        $checked = ($this->getFieldValue($name) !== false && in_array($value, (array)$this->getFieldValue($name)) ? true : ($this->getFieldValue($name) !== false ? false : $checked));
        if ($checked == true) {
            $attr = array_merge((array)$attr, array('checked' => 'checked'));
        }
        return $this->input($name, $value, $attr);
    }

    /**
     * create radio field
     * @param $name
     * @param bool|false $value
     * @param bool|false $checked
     * @param array $attr
     * @return string
     */
    public function radio($name, $value = false, $checked = false, $attr = array())
    {
        return $this->createCheckBox($name, $value, $checked, array_merge((array)$attr, array('type' => 'radio')));
    }

    /**
     * create checkbox field
     * @param $name
     * @param bool|false $value
     * @param bool|false $checked
     * @param array $attr
     * @return string
     */
    public function checkbox($name, $value = false, $checked = false, $attr = array())
    {
        return $this->createCheckBox($name, $value, $checked, array_merge((array)$attr, array('type' => 'checkbox')));
    }

    /**
     * create options tag
     * @param $value
     * @param $label
     * @param bool|false $selected
     * @param array $disabled
     * @return string
     */
    public function option($value, $label, $selected = false, $disabled = array())
    {
        $selected = (in_array($value, (array)$selected) ? 'selected' : null);
        $attr = array('value' => Str::entities($value), 'selected' => $selected);
        if ($selected == null) {
            unset($attr['selected']);
        }
        if (in_array($value, (array)$disabled)) {
            $attr['disabled'] = 'disabled';
        }
        return '<option ' . HtmlC::attr($attr) . '>' . $label . '</option>';
    }

    /**
     * create option group
     * @param $options
     * @param $label
     * @param bool|false $selected
     * @param array $disabled
     * @return string
     */
    public function optgroup($options, $label, $selected = false, $disabled = array())
    {
        $html = array();
        foreach ($options as $value => $title) {
            $html[] = $this->option($value, $title, $selected, $disabled);
        }
        return '<optgroup label="' . Str::entities($label) . '">' . implode('', $html) . '</optgroup>';
    }

    /**
     * create select
     * @param $name
     * @param $options
     * @param bool|false $selected
     * @param array $attr
     * @param array $disabled
     * @return string
     */
    public function select($name, $options, $selected = false, $attr = array(), $disabled = array())
    {
        $html = array();
        $selected = ($selected === false ? $this->getFieldValue($name) : $selected);
        foreach ($options as $value => $option) {
            if (is_array($option)) {
                $html[] = $this->optgroup($option, $value, $selected, $disabled);
            } else {
                $html[] = $this->option($value, $option, $selected, $disabled);
            }
        }
        if (substr($name, -2) == '[]') {
            $attr['multiple'] = 'multiple';
        }
        if (isset($this->attrs['select'])) {
            $attr = array_merge($attr, (array)$this->attrs['select']);
        }
        return '<select ' . HtmlC::attr(array_merge($attr, array('name' => $name))) . '>' . implode('', $html) . '</select>';
    }

    /**
     * create button
     * @param $name
     * @param $label
     * @param bool|false $value
     * @param array $attr
     * @return string
     */
    public function createButton($name, $label, $value = false, $attr = array())
    {
        $attr = (array)$attr;
        if (is_array($name)) {
            $attr = array_merge($attr, $name);
        } elseif ($name !== false) {
            $attr['name'] = $name;
        }
        if ($value !== false) {
            $attr['value'] = $value;
        }
        if (isset($this->attrs[$attr['type']])) {
            $attr = array_merge($attr, (array)$this->attrs[$attr['type']]);
        }
        return '<button ' . HtmlC::attr($attr) . '>' . $label . '</button>';
    }

    /**
     * create button normal
     * @param $name
     * @param $label
     * @param array $attr
     * @return string
     */
    public function button($label, $name = false, $attr = array())
    {
        return $this->createButton($name, $label, false, array_merge($attr,array('type' => 'button')));
    }

    /**
     * create submit button
     * @param $name
     * @param $label
     * @param bool|false $value
     * @param array $attr
     * @return string
     */
    public function submit($label, $name = false, $value = false, $attr = array())
    {
        return $this->createButton($name, $label, $value, array_merge($attr,array('type' => 'submit')));
    }

    /**
     * create reset button
     * @param $name
     * @param $label
     * @param array $attr
     * @return string
     */
    public function reset($label, $name = false, $attr = array())
    {
        return $this->createButton($name, $label, false, array_merge($attr,array('type' => 'reset')));
    }
}