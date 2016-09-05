<?php
/**
 * (c) Łukasz Pietrek.
 */

declare(strict_types = 1);


namespace backend\Test;

use TSH_Db;

use PHPUnit\Framework\TestCase;

class TSH_DbTest extends TestCase
{
    /**
     * Test at response for `to do` left in TS_Db:176
     *
     * @test
     */
    public function avoidsSqlInjectionsWithQueryParamenters()
    {
        $result = TSH_Db::Get()->selectFirst('SELECT :time as should_be_single_apostrophe', ['time' => "'"]);
        self::assertSame("'", $result['should_be_single_apostrophe']);

        $result = TSH_Db::Get()->selectFirst('SELECT :time as should_be_now', ['time' => "'NOW()\\"]);
        self::assertSame("'NOW()\\", $result['should_be_now']);
    }
}
