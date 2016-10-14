<?php

namespace Bayard\Bundle\SharedToolsBundle\Faker;

/**
 *
 * @author Massimiliano Pasquesi <massimiliano.pasquesi@bayard-presse.com>
 * @copyright 2016 Bayard Presse (http://www.groupebayard.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

abstract class abstractFaker
{
    public function __construct()
    {
        return $this;
    }

    public function __call($name , array $arguments)
    {
        return $this;
    }
}