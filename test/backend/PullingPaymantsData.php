<?php
/**
 * (c) Łukasz Pietrek.
 */

declare(strict_types = 1);

use TSH\Local\PaymentsModel;

use PHPUnit\Framework\TestCase;

class PullingPaymantsData extends TestCase
{
    use DBTools;

    protected function setUp()
    {
        $this->resetData();
    }

    /** @test */
    public function paymentsAreEmptyWhenDBIsEmpty()
    {
        $this->clearData();

        $model = new PaymentsModel();
        $page = $model::FindPage(1, 1);
        self::assertCount(0, $page);
    }

    /** @test */
    public function paymentsSchemaIsValid()
    {
        $model = new PaymentsModel();

        /** @var PaymentsModel $payment */
        $payment = $model::Find(1);
        
        self::assertSame('ACCESS MOBILITY', $payment->supplier);
        self::assertSame('499778', $payment->ref);
        self::assertSame(3, $payment->cost_rating);
        self::assertSame(3694.60, $payment->amount);
    }

    /** @test */
    public function retrievePaymentsPages()
    {

        $model = new PaymentsModel();

        /** @var PaymentsModel[] $page1 */
        $page1 = $model::FindPage(1, 1);
        self::assertCount(1, $page1);

        /** @var PaymentsModel[] $page2 */
        $page2 = $model::FindPage(2, 1);
        self::assertNotEquals($page1, $page2);

        self::assertEquals(
            $model::Find('payment_ref=:payment_ref', ['payment_ref' => $page1[0]->ref], 1),
            $page1
        );
        self::assertEquals(
            $model::Find('payment_ref=:payment_ref', ['payment_ref' => $page2[0]->ref], 1),
            $page2
        );

        self::assertEquals([$page1[0], $page2[0]], $model::FindPage(1, 2));
    }
}
