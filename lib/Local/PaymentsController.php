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
        $model = $this->model;

        $where = 1;
        $params = [];
        if ($request->supplier()) {
            $where = 'payment_supplier LIKE :supplier';
            $params['supplier'] = "%{$request->supplier()}%";
        }
        if ($request->cost_rating()) {
            $where = 'payment_cost_rating = :rating';
            $params['rating'] = $request->cost_rating();
        }

        $payments = $model::FindPage(
            $request->page(),
            $this->config['payments_per_page'],
            $where,
            $params
        ) ?: [];

        $page = new PaymentsPage(array_merge($this->config, [
            'payments' => $payments,
            'total_pages' => (int) \ceil(\TSH_Db::Get()->numRows() / (int)$this->config['payments_per_page']),
            'current_page' => $request->page(),
            'query_info' => count($payments) ? '' : $this->config['query_info::result_is_empty']
        ]));

        return $page;
    }
}