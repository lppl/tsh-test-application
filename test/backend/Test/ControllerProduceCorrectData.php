<?php
/**
 * (c) Łukasz Pietrek.
 */

declare(strict_types = 1);

namespace TSH\Local\Test;

use PHPUnit\Framework\TestCase;

use TSH\Local\PaymentsModel;
use TSH\Local\PaymentsPage;
use TSH\Local\PaymentsController;
use TSH\Local\PaymentsRequest;
use TSH\Local\TestUtil\DBTools;

class ControllerProduceCorrectData extends TestCase
{
    use DBTools;

    protected function setUp()
    {
        $this->resetData();
    }

    private $base_config = [
        'title' => 'Payments title',
        'subtitle' => 'Payments subtitle',
        'query_info::result_is_empty' => 'Sorry, query result is emtpy.',
        'payments_per_page' => 5
    ];

    /**
     * @test
     * @dataProvider pages
     */
    public function controllerOnIndexPage(
        array $config,
        PaymentsRequest $request,
        array $response
    )
    {
        $payments_on_page = $response['payments_on_page'] ?? $config['payments_per_page'];
        $total_pages = $response['total_pages'] ?? 50;


        $controller = new PaymentsController($config, new PaymentsModel());

        $page = $controller->respondTo($request);

        self::assertSame(PaymentsPage::class, get_class($page));
        self::assertSame($config['title'], $page->title);
        self::assertSame($config['subtitle'], $page->subtitle);
        self::assertCount($payments_on_page, $page->payments);
        self::assertSame($total_pages, $page->total_pages);
        self::assertSame($request->page(), $page->current_page);

        foreach ($page->payments as $payment) {
            self::assertSame(PaymentsModel::class, get_class($payment));
        }
    }

    /** @test */
    public function controllerWithQueries()
    {
        $controller = new PaymentsController($this->base_config, new PaymentsModel());

        $this->clearData();
        $this->insertCustomPayments([[
            'payment_supplier' => 'First Supplier',
            'payment_ref' => '111111',
            'payment_cost_rating' => 2,
            'payment_amount' => 1111.00
        ], [
            'payment_supplier' => 'Other Supplier',
            'payment_ref' => '222222',
            'payment_cost_rating' => 3,
            'payment_amount' => 2222.00
        ], [
            'payment_supplier' => 'Third Supplier',
            'payment_ref' => '222222',
            'payment_cost_rating' => 3,
            'payment_amount' => 2222.00
        ]]);

        $no_such_supplier = $controller->respondTo(
            new PaymentsRequest($page = 1, $supplier = 'No such Supplier')
        );

        static::assertCount(0, $no_such_supplier->payments);
        static::assertSame($this->base_config['query_info::result_is_empty'], $no_such_supplier->query_info);

        $single_supplier = $controller->respondTo(
            new PaymentsRequest($page = 1, $supplier = 'Other')
        );

        static::assertCount(1, $single_supplier->payments);
        static::assertSame('', $single_supplier->query_info);

        $no_such_rating = $controller->respondTo(
            new PaymentsRequest($page = 1, $supplier = '', $cost_rating = 1)
        );

        static::assertCount(0, $no_such_rating->payments);
        static::assertSame($this->base_config['query_info::result_is_empty'], $no_such_rating->query_info);

        $single_rating = $controller->respondTo(
            new PaymentsRequest($page = 1, $supplier = '', $cost_rating = 2)
        );

        static::assertCount(1, $single_rating->payments);
        static::assertSame('', $single_rating->query_info);

        $no_mixed_results = $controller->respondTo(
            new PaymentsRequest($page = 1, $supplier = 'Third', $cost_rating = 2)
        );

        static::assertCount(0, $no_mixed_results->payments);
        static::assertSame($this->base_config['query_info::result_is_empty'], $no_mixed_results->query_info);

        $single_mixed_result = $controller->respondTo(
            new PaymentsRequest($page = 1, $supplier = 'Third', $cost_rating = 3)
        );

        static::assertCount(1, $single_mixed_result->payments);
        static::assertSame('', $single_mixed_result->query_info);
    }

    public function pages()
    {
        return [[ // when we ask for basic config
            $this->base_config,
            new PaymentsRequest(),
            []
        ], [ // when we as for 10 payments on page
            array_merge($this->base_config, ['payments_per_page' => 10]),
            new PaymentsRequest(),
            ['total_pages' => 25]
        ], [ // ask for last page which has less records than usually
            array_merge($this->base_config, ['payments_per_page' => 10]),
            new PaymentsRequest($page = 25),
            ['payments_on_page' => 8, 'total_pages' => 25]
        ]];
    }
}
