<?php

namespace Bayard\Bundle\SharedToolsBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
//use Bayard\Bundle\SharedToolsBundle\Command\AbstractCommandClass;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Bayard\Bundle\SharedToolsBundle\Handler\CsvHandler;
use Bayard\Bundle\SharedToolsBundle\Exception\BayardSharedException as SharedException;

class CsvHandlerCommand extends AbstractCommandClass
{
    //protected $commandNamePrefix = 'pim:';
    protected $commandLabel = 'CSV Handler';
    protected $csvHandler;

    protected function configure()
    {
        $this
            ->setNamePlus('csv:handler')
            ->setDescription('Do stuff with a csv file')
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                "csv file path"
            )
            ->addArgument(
                'arguments',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                "Operation arguments"
            )
            ->addDefaultOptions()
        ;
    }

    protected function getCommandArguments()
    {
        if ($file = $this->input->getArgument('file')) {

            $this->file = $file;

            if (!file_exists($this->file)) {
                throw SharedException::fileNotFound($this->file);
            }

            if (!is_writable($this->file)) {
                throw SharedException::fileNotWritable($this->file);
            }
        }

        if ($arguments = $this->input->getArgument('arguments')) {
            $this->arguments = $arguments;
        }
    }


    protected function executeCommand()
    {
        $csvHandler = new CsvHandler($this->file);
        $method = $this->arguments[0];
        array_shift($this->arguments);

        $result = call_user_func_array([$csvHandler, $method], $this->arguments);

        print_r(compact('result'));
        $this->happyEnd();
    }

    protected function testing()
    {
        $bkp_file = $this->file . '.old';
        if (file_exists($bkp_file)) {
            unlink($bkp_file);
        }

        $csvHandler = new CsvHandler($this->file);
        $attributesNamesMapper = "ItemNumber:item_number,skuname:name";
        $attributesNamesMapper = $this->configurationFieldToArrayConverter($attributesNamesMapper);

        $headers = $csvHandler->getCsvHeaders();
        $old_line = implode(';', $headers);

        foreach ($headers as $i => $name) {
            if (array_key_exists($name, $attributesNamesMapper)) {
                $headers[$i] = $attributesNamesMapper[$name];
            }
        }

        $new_line = implode(';', $headers);

        copy($this->file, $bkp_file);
        file_put_contents($this->file, str_replace($old_line, $new_line, file_get_contents($this->file)));

    }

    protected function configurationFieldToArrayConverter($field)
    {
        $converted = array();
        $splitted = explode(',', $field);
        foreach ($splitted as $pair_value) {
            $item = explode(':', $pair_value);
            $converted[$item[0]] = $item[1];
        }

        return $converted;
    }

}