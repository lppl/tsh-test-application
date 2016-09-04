<?php
/**
 * (c) Łukasz Pietrek.
 */

declare(strict_types = 1);


namespace TSH\Local;


class PaymentsPage
{
    /** @var string */
    public $title = '';

    /** @var string */
    public $subtitle = '';

    /** @var PaymentsModel[] */
    public $payments = [];

    /** @var int */
    public $total_pages = 1;

    /** @var int */
    public $current_page = 1;

    /** @var string */
    public $query_info = '';

    /** @var int */
    public $query_cost_rating = 0;

    /** @var string */
    public $query_supplier = '';

    public function __construct(array $config)
    {
        foreach (get_object_vars($this) as $property => $default) {
            $this->$property = $config[$property] ?? $default;
        }
    }
}