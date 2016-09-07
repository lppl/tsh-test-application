<?php
/**
 * (c) Łukasz Pietrek.
 */

declare(strict_types = 1);

namespace TSH\Local\Test;

use TSH\Local\MySqlPaymentsModel;
use TSH\Local\TestUtil\DBMock;
use TSH\Local\TestUtil\DBTools;

use PHPUnit\Framework\TestCase;

class PullingPaymantsData extends TestCase
{
    use DBTools;

    private $db = [
        'payment_id' => 1,
        'payment_supplier' => 'Supplier 1',
        'payment_ref' => '67779',
        'payment_cost_rating' => 2,
        'payment_amount' => 200.00,
    ];

    /** @test */
    public function paymentsAreEmptyWhenDBIsEmpty()
    {
        $this->MockFindPage(1, "SELECT * FROM payments WHERE 1", [], 20, $willReturn = []);
        $model = new MySqlPaymentsModel();
        $page = $model->FindPage(1);
        self::assertCount(0, $page);
    }

    /** @test */
    public function paymentsSchemaIsValid()
    {
        $willReturn = $this->db;
        $this->MockFind("SELECT * FROM payments WHERE payment_id = :id", ["id" => 1], $willReturn);

        $model = new MySqlPaymentsModel();

        $payment = $model->Find(1);

        self::assertSame($willReturn['payment_supplier'], $payment->supplier);
        self::assertSame($willReturn['payment_ref'], $payment->ref);
        self::assertSame($willReturn['payment_cost_rating'], $payment->cost_rating);
        self::assertSame($willReturn['payment_amount'], $payment->amount);
    }

    /** @before */
    public function setUp()
    {
        DBMock::Get()->stopMockingMe();
    }

    /** @afterClass */
    public static function stopMockingDB()
    {
        DBMock::Get()->stopMockingMe();
    }
}
