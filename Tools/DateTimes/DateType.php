<?php

/*
 * This file is part of the Bayard SharedToolsBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bayard\Bundle\SharedToolsBundle\Tools\DateTimes;

/**
 * {@inheritDoc}
 * Extends DateTimeType Class adding and/or customizing some methods
 * The main purpose is to differenciate Dates from DateTimes
 *
 * @author Massimiliano PASQUESI <massimiliano.pasquesi@bayard-presse.com>
 */
class DateType extends DateTimeType
{
    const DEFAULT_FORMAT = 'Y-m-d';

    /**
     * Return Date in ISO8601 format
     *
     * @return String
     */
    public function __toString()
    {
        return $this->format(self::DEFAULT_FORMAT);
    }


    /**
     * Return difference between $this and $now
     *
     * @param Datetime|String $now
     * @return DateInterval
     */
    public function difference($now = 'NOW')
    {
        if(!($now instanceOf DateTime)) {
            $now = new DateTime($now);
        }

        return parent::diff($now);
    }

    /**
     * Return Age in Years
     *
     * @param Datetime|String $now
     * @return Integer
     */
    public function getAge($now = 'NOW')
    {
        return $this->difference($now)->format('%y');
    }


    /**
     * Return this date's year less a given number
     *
     * @param  integer $less
     * @return integer  year
     */
    public function yearLess($less = 0)
    {
        $current_year = $this->format('Y');

        if (is_int($less) && $less > 0) {
            $year_less = intval($current_year) - $less;
        }

        return $year_less;
    }

    /**
     * get year from this date
     *
     * @return Integer year
     */
    public function getYear()
    {
        return $this->format('Y');
    }

}