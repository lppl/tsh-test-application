<?php
/**
 * (c) Łukasz Pietrek.
 */

declare(strict_types = 1);


namespace TSH\Local;


interface PaymentsModel
{
    /**
     * @param bool $idOrWhere
     * @param array $params
     * @param bool $limit
     * @return PaymentEntity
     */
    public function Find($idOrWhere = false, $params = [], $limit = false) : PaymentEntity;

    /**
     * @param $page
     * @param int $numPerPage
     * @param int $where
     * @param array $params
     * @return PaymentEntity[]
     */
    public function FindPage($page, $numPerPage = 20, $where = 1, $params = []) : array;

    /**
     * @param PaymentEntity $payment
     * @return void
     */
    public function Save(PaymentEntity $payment);
}