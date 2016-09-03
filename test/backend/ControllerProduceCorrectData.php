<?php
/**
 * (c) Łukasz Pietrek.
 */

declare(strict_types = 1);


use PHPUnit\Framework\TestCase;

use TSH\Local\PaymentsModel;
use TSH\Local\PaymentsPage;
use TSH\Local\PaymentsController;
use TSH\Local\PaymentsRequest;

class ControllerProduceCorrectData extends TestCase
{
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


        $controller = new PaymentsController($config, new PaymentsModel());

        $page = $controller->respondTo($request);

        self::assertSame(PaymentsPage::class, get_class($page));
        self::assertSame($config['title'], $page->title);
        self::assertSame($config['subtitle'], $page->subtitle);
        self::assertCount($payments_on_page, $page->payments);

        foreach ($page->payments as $payment) {
            self::assertSame(PaymentsModel::class, get_class($payment));
        }
    }

    public function pages()
    {
        return [[ // we ask for no payments on page
            array_merge($this->base_config, ['payments_per_page' => 0]),
            new PaymentsRequest(),
            ['payments_on_page' => 0]
        ], [ // when we as for 10 payments on page
            array_merge($this->base_config, ['payments_per_page' => 10]),
            new PaymentsRequest(),
            []
        ], [ // ask for last page which has less records than usually
            array_merge($this->base_config, ['payments_per_page' => 10]),
            new PaymentsRequest($page = 25),
            ['payments_on_page' => 8]
        ]];
    }
}
