<?php

/*
 * This file is part of the Bayard SharedToolsBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bayard\Bundle\SharedToolsBundle\Tools\Strings;

/**
 * Tools for handling Strings
 *
 * @author Massimiliano PASQUESI <massimiliano.pasquesi@bayard-presse.com>
 */
class StringsTools
{
    const ENCODING = 'UTF-8';
    const CASE_TITLE = 2;
    /**
     * which characters have to be considered as dashes
     * @var array
     */
    static protected $dashes = array("-","_");

    /**
     * Convert string in given mode
     * @param  string $str
     * @param  string $mode (lowercase, uppercase, ucfirst, ucwords, camelCase, uncamelCase, upperUncamelCase)
     * @return string transformed string
     */
    static public function convert_case($str, $mode) {

        switch ($mode) {
            case 'lowercase':
            case 'lower':
                $user_func = 'mb_strtolower';
                $func_args = array(&$str);
                break;
            case 'uppercase':
            case 'upper':
                $user_func = 'mb_strtoupper';
                $func_args = array(&$str);
                break;
            case 'ucfirst':
            case 'first':
            case 'paragraph':
                $user_func = 'ucfirst';
                $func_args = array(&$str);
                break;
            case 'ucwords':
            case 'title':
                $user_func = 'mb_convert_case';
                $func_args = array(&$str, self::CASE_TITLE);
                break;
            case 'camelCase':
            case 'camelcase':
            case 'camel':
                $user_func = 'self::toCamelCase';
                $func_args = array(&$str);
                break;
            case 'uncamelCase':
            case 'uncamelcase':
            case 'uncamel':
                $user_func = 'self::fromCamelCase';
                $func_args = array(&$str);
                break;
            case 'upperUncamelCase':
            case 'upperuncamelcase':
            case 'upper_uncamel':
                $user_func = 'self::fromCamelCase';
                $func_args = array(&$str, 'upper');
                break;
            default:
                return $str;
        }

        $str = call_user_func_array($user_func, $func_args);
        return $str;
    }

    /**
     * @internal this is only for test purpose during development
     */
    private function testPregReplaceCallback()
    {
        $result = preg_replace(
            "/\{([<>])([a-zA-Z0-9_]*)(\?{0,1})([a-zA-Z0-9_]*)\}(.*)\{\\1\/\\2\}/iseU",
            "CallFunction('\\1','\\2','\\3','\\4','\\5')",
            $result
        );

        $result = preg_replace_callback(
            "/\{([<>])([a-zA-Z0-9_]*)(\?{0,1})([a-zA-Z0-9_]*)\}(.*)\{\\1\/\\2\}/isU",
            function($m) { return CallFunction($m[1], $m[2], $m[3], $m[4], $m[5]); },
            $result
        );
    }

    /**
     * @see http://php.net/manual/en/function.mb-strtoupper.php
     * @param  String $str
     * @return String transformed string
     */
    public static function toUpper($str)
    {
        return mb_strtoupper($str);
    }

    /**
     * @see http://php.net/manual/en/function.mb-strtolower.php
     * @param  String $str
     * @return String transformed string
     */
    public static function toLower($str)
    {
        return mb_strtolower($str);
    }

    /**
     * Transform CamelCase string in string_with_underscores format
     *
     * @param  String $str
     * @param  String $case lower or upper, default is lower
     * @return String transformed string
     */
    public static function fromCamelCase($str, $case = 'lower')
    {
        $str[0] = strtolower($str[0]);

        //$new_str = preg_replace('/([A-Z])/e', "'_' . strtolower('\\1')", $str);
        $new_str = preg_replace_callback('/([A-Z])/',
            function($m) { return '_' . strtolower($m[1]); },
            $str );

        $new_str = ($case == 'lower') ?
            $new_str = self::convert_case($new_str, 'lower') :
            $new_str = self::convert_case($new_str, 'upper');

        return $new_str;
    }

    /**
     * Transform string_with_dashes in CamelCase format
     * @param  string  $str
     * @param  boolean $capitaliseFirstChar if true first char is uppercase
     * @return String  transformed string
     */
    public static function toCamelCase($str, $capitaliseFirstChar = false)
    {
        $str = mb_strtolower($str);

        if ($capitaliseFirstChar) {
            $str[0] = strtoupper($str[0]);
        }

        $new_str = preg_replace_callback('/[_-]([a-z])/',
            function($m) { return strtoupper($m[1]); },
            $str );

        return $new_str;
    }

    /**
     * Transform string(_|-)with(_|-)dashes in CamelCase format
     *
     * @param  String $str
     * @return String      transformed string
     */
    static public function camelCase($str) {
        $str = preg_replace('/([a-z])([A-Z])/', "\\1 \\2", $str);
        $str = preg_replace('/([A-Z])([A-Z])/', "\\1 \\2", $str);
        $str = preg_replace('@[^a-zA-Z0-9\-_ ]+@', '', $str);
        $str = str_replace(self::$dashes, ' ', $str);
        $str = str_replace(' ', '', ucwords(strtolower($str)));
        $str = strtolower(substr($str,0,1)) . substr($str,1);
        return $str;
    }

    /**
     * Make a camelCase string imploding array of strings
     *
     * @param  Array  $pieces
     * @return String    transformed string
     */
    static public function camelCaseImplode(Array $pieces)
    {
        return self::caseImplode($pieces, 'camelCase');
    }

    /**
     * Make a string according to $case imploding array of strings
     * @todo  : make other case transformation possible (only camelCase is performed actually)
     *
     * @param  Array $pieces
     * @param  String $case
     * @return String         [description]
     */
    static public function caseImplode(Array $pieces, $case)
    {
        $str = "";

        if (in_array($case, array('camelCase', 'camelcase', 'camel'))) {
            $str .= self::camelCase(strtolower($pieces[0]));
            for ($i=1; $i < count($pieces); $i++) {
                $str .= ucfirst(self::camelCase(strtolower($pieces[$i])));
            }
        }

        return $str;
    }

    /**
     * Return a random character
     * @return char
     */
    static public function randomLetter() {
        return chr(97 + mt_rand(0, 25));
    }

    /**
     * Return a random text
     * @param  integer $length
     * @return string
     */
    static public function randomText($length = 1) {
        $random_text = '';
        if ($length < 1)
            return $random_text;

        for ($i = 1; $i <= $length; $i++) {
            $random_text .= self::randomLetter();
        }

        return $random_text;
    }

    /**
     * Return a string made of a random number of given string
     * @param  $str
     * @param  integer $max   maximum number of repetitions
     * @return string   generated string
     */
    static public function randomLetterRepetition($str, $max = 9) {

        $repeat = mt_rand(0, $max-1);

        $repeated = $str;
        for ($i=0; $i<$repeat; $i++) {
            $repeated .= $str;
        }

        return $repeated;
    }

    /**
     * Change case of all elements of given array
     *
     * @param  Array $haystack
     * @param  string $case
     * @return Array
     */
    static public function arrayChangeKeyCase(Array $haystack, $case = 'lower')
    {
        foreach ($haystack as $k => $v) {
            $new_array[self::convert_case($k, $case)] = $v;
        }

        return $new_array;
    }

    /**
     * [recursiveImplode description]
     * @param  array   $array        [description]
     * @param  string  $glue         [description]
     * @param  boolean $include_keys [description]
     * @param  boolean $trim_all     [description]
     * @return [type]                [description]
     */
    static public function recursiveImplode(array $array, $glue = ',', $include_keys = false, $trim_all = true)
    {
        $glued_string = '';
        // Recursively iterates array and adds key/value to glued string
        array_walk_recursive($array, function($value, $key) use ($glue, $include_keys, &$glued_string)
        {
            $include_keys and $glued_string .= $key.$glue;
            $glued_string .= $value.$glue;
        });
        // Removes last $glue from string
        strlen($glue) > 0 and $glued_string = substr($glued_string, 0, -strlen($glue));
        // Trim ALL whitespace
        $trim_all and $glued_string = preg_replace("/(\s)/ixsm", '', $glued_string);
        return (string) $glued_string;
    }

    /**
     * oui
     * [trimStringNumber description]
     * @param  [type] $str_number [description]
     * @param  string $separator  [description]
     * @return [type]             [description]
     */
    static public function trimStringNumber($str_number, $separator = " ")
    {
        return str_replace($separator, '', $str_number);
    }

}