<?php
/**
 * (c) Łukasz Pietrek.
 */

declare(strict_types = 1);


namespace TSH\Local;


class PaymentEntity
{
    /** @var string  */
    public $supplier = '';

    /** @var string  */
    public $ref = '';

    /** @var int  */
    public $cost_rating = 0;

    /** @var float  */
    public $amount = 0.0;

    public function __construct(array $entity)
    {
        foreach (get_object_vars($this) as $property => $default) {
            $this->$property = $entity[$property]
                ?? $entity["payment_$property"]
                ?? $default;
        }
    }


}