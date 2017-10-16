<?php

namespace Bayard\Bundle\SharedToolsBundle\Composer;

use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler as SymfonyScriptHandler;
use Composer\Script\Event;


class ScriptHandler extends SymfonyScriptHandler
{
    /**
     * Check and eventually execute doctrine:migrations.
     *
     * @param $event Event A instance
     */
    public static function checkDoctrineMigrations(Event $event)
    {
        $script_desc = "Check existance of doctrine migrations. And execute if found.";
        $event->getIO()->write($script_desc);

        $options = static::getOptions($event);
        $consoleDir = static::getConsoleDir($event, $script_desc);

        if (null === $consoleDir) {
            return;
        }
        try {
            static::executeCommand($event, $consoleDir, 'doctrine:migrations:migrate --no-interaction --allow-no-migration', $options['process-timeout']);
        } catch (\Exception $e) {
            $event->getIO()->error($e->getMessage());
        }

    }
}