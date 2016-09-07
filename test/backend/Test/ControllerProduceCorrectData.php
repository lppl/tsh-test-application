<?php
/**
 * (c) Łukasz Pietrek.
 */

declare(strict_types = 1);

namespace TSH\Local\Test;

use PHPUnit\Framework\TestCase;

use TSH\Local\MySqlPaymentsModel;
use TSH\Local\PaymentEntity;
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


        $controller = new PaymentsController($config, new MySqlPaymentsModel());

        $page = $controller->respondTo($request);

        self::assertSame(PaymentsPage::class, get_class($page));
        self::assertSame($config['title'], $page->title);
        self::assertSame($config['subtitle'], $page->subtitle);
        self::assertSame($total_pages, $page->total_pages);
        self::assertSame($request->page(), $page->current_page);


        self::assertCount($payments_on_page, $page->payments);
        foreach ($page->payments as $payment) {
            self::assertNotNull(PaymentEntity::class, get_class($payment));
        }


        self::assertCount($total_pages, $page->page_links);
        foreach ($page->page_links as $n => $link) {
            $num = $n + 1;
            self::assertSame($num === $page->current_page, $link['active']);
            self::assertSame($num === $page->current_page, $link['disabled']);
            self::assertSame($num, $link['text']);
            self::assertContains("page=$num", $link['url']);
            self::assertContains("supplier={$request->supplier()}", $link['url']);
            self::assertContains("cost_rating={$request->cost_rating()}", $link['url']);
        }

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

    /**
     * @test
     * @dataProvider queries
     */
    public function controllersMangeQueries(
        string $test_info,
        PaymentsRequest $request,
        int $result_count,
        string $query_info)
    {
        $controller = new PaymentsController($this->base_config, new MySqlPaymentsModel());

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

        $page = $controller->respondTo($request);

        static::assertCount($result_count, $page->payments);
        static::assertSame($query_info, $page->query_info);
        static::assertSame($request->supplier(), $page->query_supplier);
        static::assertSame($request->cost_rating(), $page->query_cost_rating);

    }

    public function queries()
    {
        return [
            ['Missing supplier', new PaymentsRequest(1, 'No such Supplier'), 0, $this->base_config['query_info::result_is_empty']],
            ['Found supplier', new PaymentsRequest(1, 'Other'), 1, ''],
            ['Missing cost_rating', new PaymentsRequest(1, '', 1), 0, $this->base_config['query_info::result_is_empty']],
            ['Found cost_rating', new PaymentsRequest(1, '', 2), 1, ''],
            ['Missing mixed params', new PaymentsRequest(1, 'Third', 2), 0, $this->base_config['query_info::result_is_empty']],
            ['Found mixed params', new PaymentsRequest(1, 'Third', 3), 1, ''],
        ];
    }
}
