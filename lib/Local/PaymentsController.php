<?php
/**
 * (c) Łukasz Pietrek.
 */

declare(strict_types = 1);


namespace TSH\Local;


class PaymentsController
{
    /** @var array */
    private $config = [];

    /** @var PaymentsModel */
    private $model = null;

    public function __construct(array $config, PaymentsModel $model)
    {
        $this->config = $config;
        $this->model = $model;
    }

    public function respondTo(PaymentsRequest $request) : PaymentsPage
    {
        $page = new PaymentsPage($this->config);
        $model = $this->model;
        $page->payments = $model::FindPage(
            $request->page(),
            $this->config['payments_per_page']
        );

        $page->total_pages = (int) \ceil(\TSH_Db::Get()->numRows() / (int)$this->config['payments_per_page']);
        $page->current_page = $request->page();
        return $page;
    }
}