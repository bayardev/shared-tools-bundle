<?php
namespace Bayard\Bundle\SharedToolsBundle\Tests\Tools\DateTimes;

use Bayard\Bundle\SharedToolsBundle\Tools\DateTimes\DateTimesTools;


class DateTimeToolsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getDateStringWithFormatProvider
    */
    public function testGetDateStringWithFormat($format, $textdate, $expected)
    {
        $result = DateTimesTools::getDateStringWithFormat($format, $textdate);

        $this->assertEquals($expected, $result);
    }

    public function getDateStringWithFormatProvider()
    {
        /**
         * @todo  Tester une ressource (file stream)
        */

        return array(

            'format Y/m/d H:i' => array('d/m/Y', '13-12-2013' , "13/12/2013" ),
            'format' => array('d/m/y', '17-05-16', "17/05/16"),
            'format' => array('j/n/Y', '17_5_2016', "17/5/2016"),
            'format' => array('j/n/y', '17.05.16', "17/5/16"),
            'format' => array('d/m/Y H:i', '17-05-16 1641', "17/05/2016 16:41"),
            'format' => array('d/m/y H:i:s', '17-05-16 16/42/09', "17/05/16 16:42:09"),
            'format' => array('j/n/Y G:i', 'last year', "17/5/2016 16:42"),
            'format' => array('j/n/y G:i:s', '17/5/16 16:43', "17/5/16 16:43:33"),
            'format' => array('d/m/Y H\hi', '17/05/2016',"17/05/2016 16h44"),
            'format' => array('j/n/Y G\hi', '17-05-2016 16:44', "17/5/2016 16h44"),
        );
    }

    /**
     * @dataProvider dateFromStringProvider
    */
    public function testdateFromString($textdate, $expected)
    {
        $result = DateTimesTools::dateFromString($textdate);
      	$this->assertArrayHasKey('date', $result);
        $this->assertInstanceOf('DateTime', $result['date']);
     	$this->assertEquals($expected, $result['date']);
    }

    public function dateFromStringProvider()
    {
        /**
         * @todo  Tester une ressource (file stream)
        */

        return array(

            'format Y-m-d' => array('2016-05-12', $this->createObjectDate('2016-05-12', 'Y-m-d', true)),
            'format d/m/Y' => array('12/05/2016', $this->createObjectDate('12/05/2016', 'd/m/Y', false))
        );
    }

    protected function createObjectDate($stringDate, $format, $frenchformat = false) {


        //il faut que l'objet retourne le 00 dans les temps.
        if ($frenchformat == false) {
            $date = \Datetime::createFromFormat($format, $stringDate);

        }elseif ($frenchformat == true) {
            $date = date_create($stringDate);
        }
        return $date;

    }

}