<?php

namespace Bayard\Bundle\SharedToolsBundle\Exception;

class BayardSharedException extends \Exception implements BayardSharedToolsBundleExceptionInterface
{
    public static function badClassEventParameterMsg($event, $parameter)
    {
        $msg = 'Event: ' . $event . ' called with bad arguments ! ';
        $msg .= 'Expect object of class LifecycleEventArgs . ';
        $msg .= (is_object($parameter))
            ? 'Object of class ' . get_class($args[1])
            : gettype($parameter);
        $msg .= ' given.';

        return new self($msg);
    }

    public static function parameterNotFound($parameter)
    {
        $msg = 'Parameter ' . $parameter . ' NOT FOUND !';

        return new self($msg);
    }

    public static function fileNotFound($file_path)
    {
        $msg = 'FILE ' . $file_path . ' NOT FOUND !';

        return new self($msg);
    }

    public static function fileNotWritable($file_path)
    {
        $msg = 'FILE ' . $file_path . ' IS NOT WRITABLE !';

        return new self($msg);
    }

    public static function invalidClass($expected_class, $got_class)
    {
        $msg = "Expected Object of class " . $expected_class . ". " .
            "Got '" . $got_class . "' instead !";

        return new self($msg);
    }
}