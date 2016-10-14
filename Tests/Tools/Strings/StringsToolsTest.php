<?php

namespace Bayard\Bundle\SharedToolsBundle\Tests\Tools\Strings;

use Bayard\Bundle\SharedToolsBundle\Tools\Strings\StringsTools;

class StringToolsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider toUpperProvider
    */
    public function testtoUpper($value, $expected)
    {
        $result = StringsTools::toUpper($value);
        $this->assertEquals($expected, $result);
    }

    public function toUpperProvider()
    {
        /**
         * @todo  Tester une ressource (file stream)
         */

        return array(
          	'min to MAJ' => array('miniscul', 'MINISCUL'),
        );
    }

    /**
     * @dataProvider fromCamelCaseProvider
     */
    public function testfromCamelCase($value, $case, $expected)
    {
        $result = StringsTools::fromCamelCase($value, $case);
        $this->assertEquals($expected, $result);
    }

    public function fromCamelCaseProvider()
    {
        /**
         * @todo  Tester une ressource (file stream)
         */

        return array(
          	'test par default' => array('fromCamelCase', 'lower', 'from_camel_case'),
          	'camelCase to evrything' => array('fromCamelCase', 'kjh', 'FROM_CAMEL_CASE'),
          	'camelCase to upper' => array('fromCamelCase', 'upper', 'FROM_CAMEL_CASE'),
        );
    }

    /**
     * @dataProvider toCamelCaseProvider
     */
    public function testtoCamelCase($value, $expected)
    {
        $result = StringsTools::toCamelCase($value);
        $this->assertEquals($expected, $result);
    }

    public function toCamelCaseProvider()
    {
        /**
         * @todo  Tester une ressource (file stream)
         */

        return array(
          	'test par default' => array('from-camel-case', 'fromCamelCase'),
          	'camelCase to upper' => array('from_Camel_Case', 'fromCamelCase'),
          	'camelCase to upper' => array('from-Camel-Case', 'fromCamelCase'),
          	'camelCase to upper' => array('from_camel_case', 'fromCamelCase'),
        );
    }

    /**
     * @dataProvider camelCaseProvider
     */
    public function testcamelCase($value, $expected)
    {
        $result = StringsTools::camelCase($value);
        $this->assertEquals($expected, $result);
    }

    public function camelCaseProvider()
    {
        /**
         * @todo  Tester une ressource (file stream)
         */

        return array(

          	'test par default' => array('from-camel-case', 'fromCamelCase'),
          	'camelCase to upper' => array('from_Camel_Case', 'fromCamelCase'),
          	'camelCase to upper' => array('from-Camel-Case', 'fromCamelCase'),
          	'camelCase to upper' => array('from_camel_case', 'fromCamelCase'),
          	'camelCase to upper' => array('fromCamelCase', 'fromCamelCase'),
        );
    }

    /**
     * @dataProvider camelCaseImplodeProvider
     */
    public function testCamelCaseImplode($value, $expected)
    {
        $result = StringsTools::camelCaseImplode($value);
        $this->assertEquals($expected, $result);
    }

    public function camelCaseImplodeProvider()
    {
        /**
         * @todo  Tester une ressource (file stream)
         */

        return array(
         	'test par default' => array( $this->specialStringToArray("(one,tow,tree)"), "oneTowTree"),
        );
    }

    public function specialStringToArray($str)
    {
        if (in_array(substr($str, 0, 1), array('(', '[')) &&
            in_array(substr($str, -1, 1), array(')', ']'))
        ) {
            $array = explode(',', substr(substr($str, 1), 0, -1));
        }
        return $array;

    }

    /**
     * @dataProvider arrayChangeKeyCaseProvider
     */
    public function testArrayChangeKeyCase($value, $case, $expected)
    {
        $result = StringsTools::arrayChangeKeyCase($value, $case);
        $this->assertEquals($expected, $result);
    }

    public function arrayChangeKeyCaseProvider()
    {
        /**
         * @todo  Tester une ressource (file stream)
         */

        return array(

         	'test par default' => array( $this->specialStringToArray("(one-Tow,toW-tree,tree)"), 'camelCase', $this->specialStringToArray("(oneTow,towTree,tree)")),
        );
    }


}