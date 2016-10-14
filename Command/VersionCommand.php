<?php

namespace Bayard\Bundle\SharedToolsBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Bayard\Bundle\SharedToolsBundle\Command\AbstractCommandClass;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Bayard\Bundle\SharedToolsBundle\Exception\BayardSharedException as SharedException;

class VersionCommand extends AbstractCommandClass
{
    protected $commandLabel = 'Manage Bayard App Version';
    protected $printTitle = false;
    protected $versionHelper;

    protected function configure()
    {
        $this
            ->setNamePlus('version')
            ->setDescription('Manage Bayard App Version')
            ->addDefaultOptions()
            ->addOption(
                'set',
                'u',
                InputOption::VALUE_REQUIRED,
                "SET new App Version"
            )
            ->addOption(
                'get',
                'g',
                InputOption::VALUE_NONE,
                "GET App Version"
            )
            ->addOption(
                'ask',
                'a',
                InputOption::VALUE_NONE,
                "Ask for App Version to Set"
            )
        ;
    }

    protected function getCommandArguments()
    {
    }

    protected function getCommandOptions()
    {
        parent::getCommandOptions();

        if ($this->input->getOption('set')) {
            $this->set = $this->input->getOption('set');
        } else if ($this->input->getOption('ask')) {
            $this->ask = true;
        }
    }


    protected function executeCommand()
    {
        $this->versionHelper = $this->getContainer()->get('bayard_shared.helper.version');

        if (isset($this->set) || isset($this->ask)) {
            $this->setAppVersion();
        } else {
            $this->getAppVersion();
        }
    }

    protected function getAppVersion()
    {
        $version = $this->versionHelper->getAppVersion();

        $this->io->success($version);
    }

    protected function setAppVersion()
    {
        if (isset($this->set)) {
            $new_version = $this->set;
        } else {
            $this->io->text("Current Version : " . $this->versionHelper->getAppVersion());
            $new_version = $this->io->ask("Please, enter a new Version tag");
        }

        $result = $this->versionHelper->setAppVersion($new_version);

        if ($result === false) {
            $this->io->error('Error trying to update VERSION file');
        } else {
            $this->io->success($new_version);
        }
    }

}