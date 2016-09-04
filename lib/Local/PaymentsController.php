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
            'query_info' => count($payments) ? '' : $this->config['query_info::result_is_empty'],
            'query_supplier' => $request->supplier(),
            'query_cost_rating' => $request->cost_rating(),
            'page_links' => $this->pageLinks($request, $total_pages),
            'prev_link' => $this->prevLink($request),
            'next_link' => $this->nextLink($request, $total_pages),
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

    /**
     * @param PaymentsRequest $request
     * @param int $total_pages
     * @return array [$links...]
     */
    private function pageLinks(PaymentsRequest $request, int $total_pages)
    {
        return array_map(function($page) use ($request) {
            return [
                'disabled' =>  $request->page() === $page,
                'active' => $request->page() === $page,
                'text' => $page,
                'url' => (string) new PaymentsRequest($page, $request->supplier(), $request->cost_rating())
            ];
        }, range(1, $total_pages));
    }

    /**
     * @param PaymentsRequest $request
     * @return array $link['disabled', 'active', 'text', 'url']
     */
    private function prevLink(PaymentsRequest $request) : array
    {
        return [
            'disabled' =>  $request->page() <= 1,
            'active' => false,
            'text' => '&lsaquo;',
            'url' => (string) new PaymentsRequest($request->page() - 1, $request->supplier(), $request->cost_rating())
        ];
    }


    /**
     * @param PaymentsRequest $request
     * @param int $total_pages
     * @return array $link['disabled', 'active', 'text', 'url']
     */
    private function nextLink(PaymentsRequest $request, int $total_pages) : array
    {
        return [
            'disabled' =>  $request->page() >= $total_pages,
            'active' => false,
            'text' => '&rsaquo;',
            'url' => (string) new PaymentsRequest($request->page() + 1, $request->supplier(), $request->cost_rating())
        ];
    }
}