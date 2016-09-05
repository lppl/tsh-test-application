<?php
/**
 * (c) Łukasz Pietrek.
 */

declare(strict_types = 1);


namespace TSH\Local\Test;


use PHPUnit\Framework\TestCase;
use TSH\Local\TestUtil\DBMock;
use TSH_Db;

class mockingTS_HDbIsPosssible extends TestCase
{
    /** @test */
    public function byDeafultMockDontInteractWithDb()
    {
        self::assertSame(TSH_Db::class, get_class(TSH_Db::Get()));
        self::assertSame(DBMock::class, get_class(DBMock::Get()));
        self::assertSame(TSH_Db::class, get_class(TSH_Db::Get()));
        self::assertSame(DBMock::class, get_class(DBMock::Get()));
    }

    /** @test */
    public function mockCanBeSwitchedOnAndOff()
    {
        $mock = DBMock::Get();
        $db = TSH_Db::Get();
        self::assertSame(TSH_Db::class, get_class(TSH_Db::Get()));

        $mock->mockWithMe($mock);
        self::assertSame($mock, TSH_Db::Get());

        $mock->stopMockingMe();
        self::assertSame($db, TSH_Db::Get());
    }

    /** @test */
    public function mocksCanBeUsedInPracticeWithPHPUnitProphecy()
    {
        $prophecy = $this->prophesize(DBMock::class);
        $prophecy->select('bar', 1)->willReturn('foo');
        $stub = $prophecy->reveal();

        self::assertFalse(TSH_Db::Get()->select('bar', 1));

        DBMock::Get()->mockWithMe($stub);
        self::assertSame('foo', TSH_Db::Get()->select('bar', 1));

        DBMock::Get()->stopMockingMe();
        self::assertFalse(TSH_Db::Get()->select('bar', 1));
    }

    /** @test */
    public function multipleStubsWontBreakDBWhenMockingStops()
    {
        $prophecy = $this->prophesize(DBMock::class);
        $prophecy->select('bar', 1)->willReturn('foo');
        $stub = $prophecy->reveal();

        DBMock::Get()->mockWithMe($stub);
        self::assertSame('foo', TSH_Db::Get()->select('bar', 1));


        $prophecy = $this->prophesize(DBMock::class);
        $prophecy->select('bar', 1)->willReturn('foobar');
        $other_stub = $prophecy->reveal();

        DBMock::Get()->mockWithMe($other_stub);
        self::assertSame('foobar', TSH_Db::Get()->select('bar', 1));

        DBMock::Get()->stopMockingMe();
        self::assertFalse(TSH_Db::Get()->select('bar', 1));
    }

    /**
     * @test
     * @expectedException \Prophecy\Exception\Call\UnexpectedCallException
     */
    public function whileMockingNotStubbedMethodsAreNotAvailable()
    {
        $prophecy = $this->prophesize(DBMock::class);
        $prophecy->select('bar', 1)->willReturn('foo');
        $stub = $prophecy->reveal();

        DBMock::Get()->mockWithMe($stub);
        TSH_Db::Get()->disconnect();
    }

    /** @before */
    public function byDeafultDontMockMe()
    {
        DBMock::Get()->stopMockingMe();
    }

    /** @after */
    public static function dontMockOtherTests()
    {
        DBMock::Get()->stopMockingMe();
    }
}
