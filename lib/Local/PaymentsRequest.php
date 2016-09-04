<?php
/**
 * (c) Łukasz Pietrek.
 */

declare(strict_types = 1);


namespace TSH\Local;


final class PaymentsRequest
{
    /** @var int  */
    private $page;

    /** @var string  */
    private $supplier;

    public function __construct(int $page = 1, string $supplier = '')
    {
        $this->page = $page;
        $this->supplier = $supplier;
    }

    public function page() : int
    {
        return $this->page;
    }

    public function supplier() : string
    {
        return $this->supplier;
    }
}