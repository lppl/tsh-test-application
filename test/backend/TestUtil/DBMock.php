<?php
/**
 * (c) Łukasz Pietrek.
 */

declare(strict_types = 1);


namespace TSH\Local\TestUtil;


use TSH_Db;

class DBMock extends TSH_Db
{
    /** @var TSH_Db */
    private $_backupInstance;
    private $_stub;

    private static $me;

    static public function Get() : DBMock
    {
        if (false == (static::$me instanceof static))
        {
            static::$me = new static(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
            static::$me->_backupInstance = TSH_Db::Get();
        }
        return static::$me;
    }


    public function mockWithMe(TSH_Db $stub)
    {
        TSH_Db::$_Instance = $stub;
    }

    public function stopMockingMe()
    {
        if (TSH_Db::$_Instance instanceof static) {
            TSH_Db::$_Instance = $this->_backupInstance;
            $this->_stub = null;
        }
    }
}