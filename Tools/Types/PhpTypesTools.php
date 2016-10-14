<?php

namespace Bayard\Bundle\SharedToolsBundle\Tools\Types;

use Bayard\Bundle\SharedToolsBundle\Tools\DateTimes\DateTimesTools;
use Bayard\Bundle\SharedToolsBundle\Tools\Strings\StringsTools;

/**
 *
 * @author Pasquesi Massimiliano <massimiliano.pasquesi@bayard-presse.com>
 * @copyright 2016 Bayard Presse (http://www.groupebayard.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class PhpTypesTools
{
    protected static function obviousType($var)
    {
        if (is_object($var)) {
            return gettype($var) . ":" . get_class($var);
        }

        if (is_resource($var)) {
            return gettype($var) . ":" . get_resource_type($var);
        }

        // gettype returns 'double' for a float number ;)
        if (is_float($var)) {
            return 'float';
        }

        return gettype($var);
    }

    /**
     * [guessTypeByValue description]
     * @param  [type] $var [description]
     * @return string      [description]
     */
    public static function guessTypeByValue($var)
    {
        $var = self::transtypeByValue($var);
        return self::obviousType($var);
    }

    /**
     * [transtypeByValue description]
     * @param  [type] $var [description]
     * @return [type]      [description]
     */
    public static function transtypeByValue($var)
    {
        if (is_string($var)) {
            $var = trim($var);
            $tmp = StringsTools::trimStringNumber($var);
            $var = (is_numeric($tmp))? $tmp : $var;
        }

        if (is_numeric($var)) {

            if (!is_int($var) && !is_float($var)) {
                /**
                 * Check IF $var is a "FLOAT" or an "INTEGER" in a STRING format
                 * ex : "12.3" | "12"
                 */
                if (floatval($var) == intval($var)) {
                    $var = intval($var);
                }
                else {
                    $var = floatval($var);
                }
            }

        }
        else if (is_string($var)) {
            /**
             * Check IF $var is a "FLOAT" with , (comma)
             * ex : "12,5698"
             */
            if (strpos($var, ',') !== false) {

                $avar = explode(',', $var);
                if (count($avar == 2)) {
                    if (is_numeric($avar[0]) && is_numeric($avar[1])) {
                        $var = str_replace(',', '.', $var);
                        $var = floatval($var);
                    }
                }
            }
            /**
             * Check IF $var is a "DATE" or "DATETIME"
             * ex : "2015/12/14 12:12"
             */
            // else if (strtotime($var) !== false) {

            //     $parsedate = date_parse($var);
            //     if (!array_key_exists('relative', $parsedate)) {
            //         $var = date_create($var);
            //     }
            // }
            $datetest = DateTimesTools::isDate($var, true);
            if ($datetest !== false) {
                $var = $datetest;
            }
        }

        return $var;
    }


    /**
     * [detectBooleanInString description]
     * @param  string $str [description]
     * @return string|boolean      [description]
     */
    public static function detectBooleanInString($str)
    {
        if (strtolower($str) === "true") {
            return true;
        } else if (strtolower($str) === "false") {
            return false;
        } else {
            return $str;
        }
    }

    /**
     * [detectArrayInString description]
     * @param  [type] $string [description]
     * @return string|array         [description]
     */
    public static function detectArrayInString($string)
    {
        if ( in_array(substr($string, 0, 1), array('(', '[')) &&
            in_array(substr($string, -1, 1), array(')', ']'))
        ) {
            return explode(',', substr(substr($string, 1), 0, -1));
        }

        return $string;
    }

}