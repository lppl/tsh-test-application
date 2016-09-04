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
        list($total_pages, $payments) = $this->retrievePaymentsAndTotalPages($request);

        $page = new PaymentsPage(array_merge($this->config, [
            'payments' => $payments,
            'total_pages' => $total_pages,
            'current_page' => $request->page(),
            'query_info' => count($payments) ? '' : $this->config['query_info::result_is_empty']
        ]));

        return $page;
    }

    /**
     * @param PaymentsRequest $request
     * @return array [$total_pages, $payments]
     */
    private function retrievePaymentsAndTotalPages(PaymentsRequest $request):array
    {
        $model = $this->model;

        list($where, $params) = $this->query($request);

        $payments = $model::FindPage(
            $request->page(),
            $this->config['payments_per_page'],
            $where,
            $params
        ) ?: [];

        $total_pages = (int)\ceil(\TSH_Db::Get()->numRows() / (int)$this->config['payments_per_page']);

        return [$total_pages, $payments];
    }

    /**
     * @param PaymentsRequest $request
     * @return array [$where, $params]
     */
    private function query(PaymentsRequest $request)
    {
        $where = 1;
        $where_clauses = [];
        $params = [];

        if ($request->supplier()) {
            $where_clauses[] = 'payment_supplier LIKE :supplier';
            $params['supplier'] = "%{$request->supplier()}%";
        }
        if ($request->cost_rating()) {
            $where_clauses[] = 'payment_cost_rating = :rating';
            $params['rating'] = $request->cost_rating();
        }

        $where = count($where_clauses) ? implode(' AND ', $where_clauses) : $where;

        return [$where, $params];
    }
}