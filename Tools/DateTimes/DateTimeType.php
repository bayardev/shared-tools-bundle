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
 * Extends PHP DateTime Class adding and/or customizing some methods
 *
 * @author Massimiliano PASQUESI <massimiliano.pasquesi@bayard-presse.com>
 */
class DateTimeType extends \DateTime
{
    const DEFAULT_FORMAT = 'Y-m-d H:i:s';

    /**
     * Return DateTime object from formatted string
     *
     * @param  string $string
     * @return DateTime object
     */
    public static function createFromString($string)
    {
        $date_time_created = self::createFromFormat(self::DEFAULT_FORMAT, $string);

        return new self($date_time_created->getTimestamp());
    }

    /**
     * Return DateTime in default format
     *
     * @return string
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

}