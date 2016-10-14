<?php

namespace Bayard\Bundle\SharedToolsBundle\Command;

use Bayard\Bundle\SharedToolsBundle\Command\AbstractToolsCommandClass;
use Bayard\Bundle\SharedToolsBundle\Tools\Types\PhpTypesTools;

class PhpTypesToolsCommand extends AbstractToolsCommandClass
{
    protected $toolsClassName = 'PhpTypesTools';
    protected $nameSpace = 'Types';
    protected $classNameMethod = 'PhpTypesTools';

    protected function configure()
    {
    	parent::configure();

    	$this
    		->setNamePlus('phptype');
    }

}