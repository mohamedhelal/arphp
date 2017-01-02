<?php
/**
 * -----------------------------------
 *  project name framework
 * -----------------------------------
 * File:Str.php
 * User: mohamed
 */

namespace ArPHP\Support;


abstract class Str
{
    /**
     * Convert all applicable characters to HTML entities
     * @param $string
     * @return string
     */
    public static function entities($string){
        return htmlentities($string, ENT_QUOTES, 'UTF-8', false);
    }

    /**
     * check if string start with
     * @param $string
     * @param $needle
     * @return bool
     */
    public static function startWith($string,$needle){
        return (strpos($string,$needle) === 0);
    }

    /**
     * clean string from html
     * @param $str
     * @return mixed
     */
    public static function cleanFromHtml($str){
        return preg_replace('/<\/?([^>]+)>/is','',$str);
    }

    /**
     * @param $text
     * @param int $limit
     * @param string $delimeter
     * @return string
     */
    public static function sliceWords($text,$limit = 50,$delimeter = ' '){
        $text = static::cleanFromHtml($text);
        $split = preg_split('/[\s|\.]+/i',$text);
        return implode($delimeter,array_slice($split,0,$limit));
    }
}