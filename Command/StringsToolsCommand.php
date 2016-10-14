<?php

namespace Bayard\Bundle\SharedToolsBundle\Command;

use Bayard\Bundle\SharedToolsBundle\Command\AbstractToolsCommandClass;
use Bayard\Bundle\SharedToolsBundle\Tools\Strings\StringsTools;

class StringsToolsCommand extends AbstractToolsCommandClass
{
    protected $toolsClassName = 'StringsTools';
    protected $nameSpace = 'Strings';
    protected $classNameMethod = 'StringsTools';


    protected function configure()
    {
    	parent::configure();

    	$this
    		->setNamePlus('strings');
    }

}