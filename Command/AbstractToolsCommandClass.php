<?php

namespace Bayard\Bundle\SharedToolsBundle\Command;

use Bayard\Bundle\SharedToolsBundle\Command\AbstractCommandClass;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Bayard\Bundle\SharedToolsBundle\Console\Style\BayardStyle;
use Bayard\Bundle\SharedToolsBundle\Tools\Strings\StringsTools;
use Bayard\Bundle\SharedToolsBundle\Tools\DateTimes\DateTimesTools;
use Bayard\Bundle\SharedToolsBundle\Tools\Types\PhpTypesTools;
use Bayard\Bundle\SharedToolsBundle\Exception\BayardSharedException as Exception;

abstract class AbstractToolsCommandClass extends AbstractCommandClass
{
    protected $methodesList;
    protected $commandNamePrefix = 'tools:';
    protected $toolsClassName = '';
    protected $nameSpace = '';
    protected $classNameMethod = '';

    const NAMESPACE_COMMUN = 'Bayard\Bundle\SharedToolsBundle\Tools';

    /**
     * @todo Changer le type de l'argument 'parameters' en InputArgument::IS_ARRAY | InputArgument::OPTIONAL
     */
    protected function configure()
    {
        $this
            ->setDescription('Execut all method of '.$this->toolsClassName.' class')
            ->addArgument('method',
           			   InputArgument::OPTIONAL,
           			   "Method to be executed (use -l to have the list of availables methods)."
            )
            ->addArgument('parameters',
      	    		    InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
    	    		   "List of method's parameters separated by a space"

            )
            ->addDefaultOptions()
            ->addOption('list-methods',
    	                'l',
    	                InputOption::VALUE_NONE,
    	                'List all method '.$this->toolsClassName);
    }

    protected function getFullNameSpace() {

    	return self::NAMESPACE_COMMUN.'\\'.$this->nameSpace.'\\'.$this->toolsClassName;
    }

    /**
     * @todo  1. J'ai coorigé : j'ai ajouté parent::getCommandOptions()
     *        2. J'ai deplacé l'execution de l'option 'list-attributes' ici !
     */
    protected function getCommandOptions()
    {
        parent::getCommandOptions();

        if ($this->input->getOption('list-methods')) {
            $this->listMethods();
            $this->happyEnd();
        }
    }

    /**
     * @todo  On doit pouvoir faire la distinction entre '123' et 123, '123.33' et 123.33
     *        ça va peut être se resoudre avec l'utilisation de InputArgument::IS_ARRAY ...
     */
    protected function getCommandArguments() {

        if ($method = $this->input->getArgument('method')) {
            $this->method = $method;

        }

        if ($parameters = $this->input->getArgument('parameters')) {

            $this->parameters = $parameters;

            foreach ($this->parameters as $key => $param) {
                $param = PhpTypesTools::detectArrayInString($param);

                switch (true) {
                    case is_array($param) :
                        $this->parameters[$key] = $param;
                        break;
                    // case is_bool(PhpTypesTools::detectBooleanInString($param)):
                    //     $this->parameters[$key] = PhpTypesTools::detectBooleanInString($param);
                    //     break;
                    case in_array($param, array('true', 'false')):
                        $this->parameters[$key] = ($param == 'true')? true : false;
                        break;
                    case is_numeric($param):
                        $this->parameters[$key] = (floatval($param) == intval($param))? intval($param) : floatval($param);
                        break;
                    default:
                        continue;
                        break;
                }
            }
        }

        else{
        	$this->parameters = array();
        }

    }

    protected function executeCommand()
    {
        if (!is_string($this->method) || !in_array($this->method, $this->getMethodList())) {

            $this->io->error("Method '" . print_r($this->method, true) . "' is NOT valid !");
            return -1;
        }

        else{
            $parameters = array();
            $result = call_user_func_array(array($this->getFullNameSpace() ,$this->method), $this->parameters);
            var_dump($result);
        }

    }

    protected function listMethods($print = true)
    {
        $this->getMethodList();

        if ($print) {
            $this->io->listing($this->methodesList);
        }
    }

    protected function getMethodList()
    {
        $this->methodesList = get_class_methods($this->getFullNameSpace());
        return $this->methodesList;
    }
}