<?php

namespace Bayard\Bundle\SharedToolsBundle\Logger;

use Monolog\Processor\WebProcessor;

class BayardWebProcessor extends WebProcessor
{
    /**
     * @var string
     */
    private $app_name;

    /**
     * {@inheritdoc}
     * @param string|null kernel parameter kernel.root_dir
     */
    public function __construct($serverData = null, array $extraFields = null, $kernel_root_dir = null, $requestStack = null)
    {
        $this->app_name = (null === $kernel_root_dir) ?
            "app" :
            basename(dirname($kernel_root_dir));

        $this->requestStack = $requestStack;

        parent::__construct($serverData, $extraFields);
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(array $record)
    {
        if (!isset($this->serverData['REQUEST_URI'])) {
            $record['extra'] = ['server' => $this->app_name];
            return $record;
        }

        if (  !array_key_exists('HTTP_CLIENT_IP', $this->serverData) && ($request = $this->requestStack->getCurrentRequest()) )
        {
            $this->addExtraField('client_ip', 'HTTP_CLIENT_IP');
            $this->serverData['HTTP_CLIENT_IP'] = $request->getClientIp();

        } else if (array_key_exists('HTTP_CLIENT_IP', $this->serverData)) {
            $this->addExtraField('client_ip', 'HTTP_CLIENT_IP');
        }

        if (array_key_exists('HTTP_X_FORWARDED_FOR', $this->serverData)) {
            $this->addExtraField('forwarded_for', 'HTTP_X_FORWARDED_FOR');
        }

        $record = parent::__invoke($record);
        return $record;
    }
}