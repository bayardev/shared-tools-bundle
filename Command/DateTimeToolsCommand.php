<?php

namespace Bayard\Bundle\SharedToolsBundle\Command;

use Bayard\Bundle\SharedToolsBundle\Command\AbstractToolsCommandClass;
use Bayard\Bundle\SharedToolsBundle\Tools\DateTimes\DateTimesTools;

class DateTimeToolsCommand extends AbstractToolsCommandClass
{
    protected $toolsClassName = 'DateTimesTools';
    protected $nameSpace = 'DateTimes';
    protected $classNameMethod = 'DateTimesTools';

    protected function configure()
    {
    	parent::configure();

    	$this
    		->setNamePlus('datetime');
    }
}