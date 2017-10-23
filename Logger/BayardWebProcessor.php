<?php

namespace Bayard\Bundle\SharedToolsBundle\Logger;

use Monolog\Processor\WebProcessor;

class BayardWebProcessor extends WebProcessor
{
    /**
     * @var string|null
     */
    private $kernel_project_dir;
    /**
     * @var string
     */
    private $app_name;

    /**
     * {@inheritdoc}
     * @param string|null kernel parameter kernel.project_dir
     */
    public function __construct($serverData = null, array $extraFields = null, $kernel_project_dir = null)
    {
        $this->kernel_project_dir = $kernel_project_dir;
        $this->app_name = (null === $kernel_project_dir) ? "app" : basename($kernel_project_dir);
        parent::__construct($serverData, $extraFields);
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(array $record) {

        if (!isset($this->serverData['REQUEST_URI'])) {
            $record['extra'] = ['server' => $this->app_name];
            return $record;
        }

        return parent::__invoke($record);
    }
}