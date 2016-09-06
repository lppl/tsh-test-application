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
    
    /** @var int  */
    private $cost_rating;

    public function __construct(int $page = 1, string $supplier = '', int $cost_rating = 0)
    {
        $this->page = max(1, $page);
        $this->supplier = filter_var($supplier, FILTER_SANITIZE_SPECIAL_CHARS);
        $this->cost_rating = $cost_rating;
    }

    public function page() : int
    {
        return $this->page;
    }

    public function supplier() : string
    {
        return $this->supplier;
    }

    public function cost_rating() : int
    {
        return $this->cost_rating;
    }

    public function __toString() : string
    {
        return sprintf(
            '?page=%s&supplier=%s&cost_rating=%s',
            $this->page,
            $this->supplier,
            $this->cost_rating
        );
    }


}