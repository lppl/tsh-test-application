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

    public function __construct(array $config)
    {
        foreach(get_object_vars($this) as $property => $default) {
            $this->$property = $config[$property] ?? $default;
        }
    }


}