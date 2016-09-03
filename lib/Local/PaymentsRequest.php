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

    public function __construct(int $page = 1)
    {
        $this->page = $page;
    }

    public function page() : int
    {
        return $this->page;
    }
}