<?php
/**
 *#--------------------------------
 * Project name phpframe
 *#--------------------------------
 * Created by mohamed.
 * File name  html.php
 * Date       21/07/15
 */

namespace ArPHP\Html;


use ArPHP\Support\Str;

 class Html
{

     /**
      * create javascript link to file
      * @param $script
      * @param bool|false $id
      * @return null|string
      */
    public  function script($script,$id = false)
    {
        if (is_array($script)) {
            $content = null;
            foreach ($script as $id => $file) {
                $content .= $this->script($file,$id) . "\n";
            }
            return $content;
        } else {
            $id = (is_numeric($id) || $id == false ? str_replace(array('.','_'),'-',basename($script)) : $id);
            return ' <script id="'.$id.'" src="' . $script . '" type="text/javascript"></script>';
        }
    }

    /**
     * get script tags
     * @param $context
     * @return string
     */
    public  function scriptTag($context){
        return ' <script  type="text/javascript">'.$context.'</script>';
    }

     /**
      * create style sheet lint to file
      * @param $style
      * @param bool $id
      * @param array $attributes
      * @return null|string
      */
    public  function style($style,$id = false,$attributes = [])
    {
        if(!isset($attributes['media'])){
            $attributes['media'] = 'all';
        }
        if (is_array($style)) {
            $content = null;
            foreach ($style as $id => $file) {
                $content .= $this->style($file,$id,$attributes) . "\n";
            }
            return $content;
        } else {
            $id = (is_numeric($id) || $id == false ? str_replace(array('.','_'),'-',basename($style)) : $id);
            return '<link id="'.$id.'" href="' . $style . '" rel="stylesheet" type="text/css" '. static::attr($attributes). '>';
        }
    }

     /**
      * create html attribute and value
      * @param array $attributes
      * @return null|string
      */
    public  function attr($attributes = array()){
        $content = null;
        foreach((array)$attributes as $key => $val){
            $content .= $key.' = "'.Str::entities($val).'"  ' ;
        }
        return $content;
    }
     /**
      * @param $attributes
      * @return string
      */
    public  function metaTag($attributes){
        return '<meta '.$this->attr($attributes). '/>';
    }
}