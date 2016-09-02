<?php
/**
 * (c) Łukasz Pietrek.
 */

declare(strict_types = 1);


/**
 * @property string $supplier
 * @property string $ref
 * @property int $cost_rating
 * @property float $amount
 */
class PaymentsModel extends TSH_Model
{
    public static $_TableName = 'payments';
    public static $_ItemName = 'payment';

    public function setFromArray(array $array)
    {
        parent::setFromArray([
            'payment_supplier' => $array['payment_supplier'],
            'payment_ref' => $array['payment_ref'],
            'payment_cost_rating' => (int) $array['payment_cost_rating'],
            'payment_amount' => (float) $array['payment_amount'],
        ]);
    }
}