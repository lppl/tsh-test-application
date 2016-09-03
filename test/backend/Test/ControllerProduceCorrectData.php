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

    public function pages()
    {
        return [[ // when we ask for basic config
            $this->base_config,
            new PaymentsRequest(),
            []
        ],[ // when we as for 10 payments on page
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
