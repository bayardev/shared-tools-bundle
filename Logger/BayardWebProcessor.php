<?php

namespace Bayard\Bundle\SharedToolsBundle\Logger;

use Monolog\Processor\WebProcessor;

class BayardWebProcessor extends WebProcessor
{
    private $servername;

    /**
     * {@inheritdoc}
     */
    public function __invoke(array $record) {

        if (!isset($this->serverData['REQUEST_URI'])) {
            $servername = shell_exec('stat -c %U'.__FILE__);
            $record['extra'] = ['server' => $servername];
            return $record;
        }

        return parent::__invoke($record);
    }
}