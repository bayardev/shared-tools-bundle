<?php

namespace Bayard\Bundle\SharedToolsBundle\Tests\Tools\Types;

use Bayard\Bundle\SharedToolsBundle\Tools\Types\PhpTypesTools;

class PhpTypesToolsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider guessTypeByValueProvider
     */
    public function testGuessTypeByValue($value, $expected)
    {
        $result = PhpTypesTools::guessTypeByValue($value);

        $this->assertEquals($expected, $result);
    }

    public function guessTypeByValueProvider()
    {
        /**
         * @todo  Tester une ressource (file stream)
         */

        return array(
            'string datetime format Y/m/d H:i' => array("2015/12/14 12:12", 'object:DateTime'),
            'string datetime format ISO 8601 date' => array("2016-03-03T23:00:00Z", 'object:DateTime'),
            'string datetime relative' => array("last Year", 'string'),
            'generic object' => array(new \stdClass(), 'object:stdClass'),
            'array' => array(array(), 'array'),
            'string' => array('I am a string', 'string'),
            'integer' => array(123, 'integer'),
            'integer as a string' => array("123", 'integer'),
            'float' => array(123.56, 'float'),
            'float as a string' => array("123.56", 'float'),
            'float as a string with comma' => array("123,56", 'float'),
            'null' => array(null, 'NULL'),
            'boolean' => array(true, 'boolean'),
        );
    }

    /**
     * @dataProvider transtypeByValueProvider
     */
    public function testTranstypeByValue($value, $expected)
    {
        $result = PhpTypesTools::transtypeByValue($value);

        $this->assertSame($expected, $result);
    }

    public function transtypeByValueProvider()
    {
        /**
         * @todo  Tester une ressource (file stream)
         */

        return array(
            'string datetime relative' => array("last Year", "last Year"),
            'array' => array(array(), array()),
            'string' => array('I am a string', 'I am a string'),
            'integer' => array(123, 123),
            'integer as a string' => array("123", 123),
            'float' => array(123.56, 123.56),
            'float as a string' => array("123.56", 123.56),
            'float as a string with comma' => array("123,56", 123.56),
            'null' => array(null, null),
            'boolean' => array(true, true),
        );
    }

    /**
     * @dataProvider transtypeByValueProvider2
     */
    public function testTranstypeByValue2($value, $expected)
    {
        $result = PhpTypesTools::transtypeByValue($value);

        $this->assertEquals($expected, $result);
    }

    public function transtypeByValueProvider2()
    {
        /**
         * @todo  Tester une ressource (file stream)
         */

        return array(
            'string datetime format Y/m/d H:i' => array("2015/12/14 12:12", new \DateTime("2015/12/14 12:12")),
            'string datetime format ISO 8601 date' => array("2016-03-03T23:00:00Z", new \DateTime("2016-03-03T23:00:00Z")),
            'generic object' => array(new \stdClass(), new \stdClass()),
        );
    }
    
}