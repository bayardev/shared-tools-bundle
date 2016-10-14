<?php

/*
 * This file is part of the Bayard SharedToolsBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bayard\Bundle\SharedToolsBundle\Tools\DateTimes;

/**
 * Tools for handling DateTimes objects
 *
 * @author Massimiliano PASQUESI <massimiliano.pasquesi@bayard-presse.com>
 */
class DateTimesTools
{
    /**
     * Define various french DateTime formats
     * @var array
     */
    protected static $frenchFormats = array(

        'd/m/Y', 'd/m/y', 'j/n/Y', 'j/n/y',
        'd/m/Y H:i', 'd/m/y H:i:s', 'j/n/Y G:i', 'j/n/y G:i:s',
        'd/m/Y H\hi', 'j/n/Y G\hi',
    );

    /**
     * get now datetime as string
     * @param  String $format
     * @return String
     */
    public static function getNowAsFormatString($format)
    {
        $now = new \DateTime();
        $stringdate = strval($now->format($format));

        return $stringdate;
    }

    /**
     * transforme given string date by given format
     * @param  String $format
     * @param  String $textdate
     * @return String
     */
    public static function getDateStringWithFormat($format, $textdate)
    {
        $date = self::dateFromString($textdate, true);
        if (is_array($date) && array_key_exists('date', $date)) {
            $date = $date['date']->format($format);
        }

        return $date;
    }

    /**
     * get DateTime object with format info by given string date
     * @param  String  $textdate
     * @param  boolean $accept_relative
     * @param  boolean $test_french_format
     * @return Array|bool  array containing format info and datetime object or FALSE
     */
    public static function dateFromString($textdate, $accept_relative = false, $test_french_format = true)
    {
        /*
         * If $test_french_format is TRUE
         * we test before if the date is in a french format
         * because otherwise we can have a bad date (for ex. month and day inversed or truncated)
        */
        if ($test_french_format === true) {
            foreach (self::$frenchFormats as $frformat) {
                if (($newdate = \DateTime::createFromFormat($frformat, $textdate)) !== false) {
                    return array('format' => $frformat, 'date' => $newdate);
                }
            }
        }

        if (strtotime($textdate) !== false) {

            $parsedate = date_parse($textdate);

            if ( $parsedate['error_count'] == 0 ) {
                if ($accept_relative === true && array_key_exists('relative', $parsedate)) {
                    return array('format' => 'relative', 'date' => date_create($textdate));
                } else if ($accept_relative !== true && array_key_exists('relative', $parsedate)) {
                    return false;
                }

                return array('format' => 'not french', 'date' => date_create($textdate));
            }

        }

        return false;
    }

    /**
     * Test if given string is date
     * @param  string  $textdate
     * @param  boolean $return_date
     * @return boolean|DateTime true/false or DateTime object
    */
    public static function isDate($textdate, $return_date = false)
    {
        $datetest = self::dateFromString($textdate);

        if (is_array($datetest) && array_key_exists('date', $datetest)) {
            return ($return_date === true)? $datetest['date'] : true;
        }

        return false;
    }

    /**
     * Test if Date have Time
     *
     * @param  Array $parsedate  can get it from date_parse php function
     * @return bool
     */
    public static function haveTime($parsedate)
    {
        $timekeys = ['hour', 'minute', 'second', 'fraction'];
        foreach ($timekeys as $timekey) {
            if (is_numeric($parsedate[$timekey]) && floatval($parsedate[$timekey]) > 0) {
                return true;
            }
        }

        return false;
    }
}