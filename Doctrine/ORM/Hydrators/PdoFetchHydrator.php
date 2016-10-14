<?php

namespace Bayard\Bundle\SharedToolsBundle\Doctrine\ORM\Hydrators;

use Doctrine\ORM\Internal\Hydration\AbstractHydrator;

class PdoFetchHydrator extends AbstractHydrator
{
    protected static $pdoFetchModesList = [
        \PDO::FETCH_ASSOC,
        \PDO::FETCH_BOTH,
        \PDO::FETCH_BOUND,
        \PDO::FETCH_CLASS,
        \PDO::FETCH_INTO,
        \PDO::FETCH_LAZY,
        \PDO::FETCH_NAMED,
        \PDO::FETCH_NUM,
        \PDO::FETCH_OBJ,
    ];

    protected static $defaultPdoFetchMode = \PDO::FETCH_BOTH;
    protected static $pdoFetchMode;

    protected function hydrateAllData()
    {
        return $this->_stmt->fetchAll(self::getPdoFetchMode());
    }

    public static function setPdoFetchMode($mode = null)
    {
        if (is_null($mode) || !in_array($mode, self::$pdoFetchModesList)) {
            self::$pdoFetchMode = self::$defaultPdoFetchMode;
        } else {
            self::$pdoFetchMode = $mode;
        }
    }

    public static function getPdoFetchMode()
    {
        if (!isset(self::$pdoFetchMode)) {
            self::$setPdoFetchMode();
        }

        return self::$pdoFetchMode;
    }

    public function __destruct()
    {
       self::$pdoFetchMode = null;
    }
}