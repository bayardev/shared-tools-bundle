<?php

namespace Bayard\Bundle\SharedToolsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Bayard\Bundle\SharedToolsBundle\Console\Style\BayardStyle;
use Bayard\Bundle\SharedToolsBundle\Exception\BayardSharedException as Exception;

abstract class AbstractCommandClass extends ContainerAwareCommand
{
    const BAYARD_COMMAND_PREFIX = 'bayard:';
    const _Y_ = "\033[0;33m";
    const _Z_ = "\033[0m";
    protected $commandNamePrefix = '';
    protected $commandLabel = 'Command';
    protected $showSysinfo = false;
    protected $printTitle = true;

    abstract protected function executeCommand();
    abstract protected function getCommandArguments();

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->initCommand($input, $output);

        // init CPU Usage
        $this->io->sysInfoStart();
        // Show CPU and Memory Usage
        $this->io->sysInfo();

        if ($this->printTitle) {
            $this->outputCommandTitle();
        }

        $this->executeCommand();

        // Show CPU and Memory Usage
        $this->io->sysInfo();
    }

    protected function outputCommandTitle()
    {
        $this->io->writeOut('Start ' . $this->commandLabel . ' Execution', 'title');
    }

    protected function initCommand(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->output->setDecorated(true);
        $this->io = new BayardStyle($input, $output);

        $this->getCommandOptions();
        $this->getCommandArguments();
    }

    protected function addDefaultOptions()
    {
        $this->addOption(
            'show-sysinfo',
            'S',
            InputOption::VALUE_NONE,
            'Show debug messages, like sysinfo'
        );

        return $this;
    }

    protected function getCommandOptions()
    {
        if ($this->input->getOption('show-sysinfo')) {
            $this->showSysinfo = true;
            $this->io->setShowSysinfo(true);
        }

    }

    protected function setNamePlus($name)
    {
        $prefix = $this->getCommandNamePrefix();

        $name = $prefix . $name;

        return $this->setName($name);
    }

    protected function getCommandNamePrefix()
    {
        $prefix = self::BAYARD_COMMAND_PREFIX . $this->commandNamePrefix;

        return $prefix;
    }

    protected function notYet($what = null)
    {
        $what = (is_null($what))? $this->getName() : $what;
        $this->io->warning("Sorry but '".$what."' is not yet implemented !");
    }

    protected function deprecated()
    {
        $this->io->warning("Sorry but '".$this->getName()."' is no more exploitable !");
    }

    protected function happyEnd($messages = array())
    {
        foreach ($messages as $msg) {
            $this->io->setSuccessMessage($msg);
        }
        // Print Success Messages
        $this->io->printSuccessMessage();
        // Show CPU and Memory Usage
        $this->io->sysInfo();

        exit();
    }

    protected function getRootDir($with_final_slash = false)
    {
        $rootdir = $this->getContainer()->getParameter('kernel.root_dir');
        $rootdir = realpath($rootdir . '/..');

        return ($with_final_slash === true) ?
            $rootdir . '/' :
            $rootdir ;
    }

}