<?php

namespace Bayard\Bundle\SharedToolsBundle\Console\Style;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Bayard\Bundle\SharedToolsBundle\Tools\SysInfos\SysInfosTools as SY;

class BayardStyle extends SymfonyStyle
{
    protected $showSysinfo = false;
    protected $successMessage = array();

    public function setShowSysinfo($show_sysinfo = true)
    {
        if ($show_sysinfo === true) {
            $this->showSysinfo = true;
        } else {
            $this->showSysinfo = false;
        }
    }

    public function verboseOut($var)
    {
        if ($this->getVerbosity() >= self::VERBOSITY_VERBOSE) {
            if (empty($var)) {
                print_r("(" . gettype($var) . ") " . $var);
                echo "\n";
            }
            else {
                print_r($var);
                echo "\n";
            }
        }
    }

    public function writeOut($message, $type = 'text')
    {
        if (method_exists($this, $type)) {
            $this->$type($message);
        } else {
            $this->text($message);
        }

        if (!in_array($type, array('title', 'section'))) {
            $this->newLine();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function text($message)
    {
        if (!is_array($message)) {
            $this->writeln(sprintf('%s', $message));

            return;
        }

        foreach ($message as $element) {
            $this->text($element);
        }
    }

    /**
     * Possible colors : (black, red, green, yellow, blue, magenta, cyan, white, default)
     * @param  [type] $message [description]
     * @param  [type] $style   [description]
     * @return [type]          [description]
     */
    public function colorText($message, $style)
    {
         if (!is_array($message)) {
            $this->writeln(sprintf('<%s>%s</>', $style, $message));

            return;
        }

        foreach ($message as $element) {
            $this->colorText($element, $style);
        }
    }

    public function greenText($message)
    {
        $style = 'fg=green';
        $this->colorText($message, $style);
    }

    public function cyanText($message)
    {
        $style = 'fg=cyan';
        $this->colorText($message, $style);
    }

    public function yellowText($message)
    {
        $style = 'fg=yellow';
        $this->colorText($message, $style);
    }

    /**
     * {@inheritdoc}
     */
    public function warning($message)
    {
        $this->block($message, 'WARNING', 'fg=red;bg=yellow', ' ', true);
    }

    public function sysInfoStart()
    {
        // init CPU Usage
        SY::onRequestStart();
    }

    public function sysInfo()
    {
        if ($this->showSysinfo === true) {
            $style = 'fg=magenta;bg=white';
            $message = "CPU : " . SY::getCpuUsage();
            $message .= " | MEM : " . SY::getMemoryUsage();
            $message .= " | PEAK-MEM : " . SY::getMemoryPeakUsage();
            $message .= " | LOAD-AVERAGE : " . SY::getServerCpuUsage(true);

            $this->block($message, 'SYSINFO', 'bg=magenta;fg=white', ' ', true);
        }
    }

    public function setSuccessMessage($msg)
    {
        $this->successMessage[] = $msg;
    }

    public function getSuccessMessage()
    {
        if (!empty($this->successMessage)) {
            return implode("\n", $this->successMessage);
        }

        return false;
    }

    public function printSuccessMessage()
    {
        if (!empty($this->successMessage)) {
            $this->success($this->getSuccessMessage());
        }
    }

}