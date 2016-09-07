<?php
/**
 * (c) Łukasz Pietrek.
 */

declare(strict_types = 1);

namespace TSH\Local;


class MySqlPaymentsModel implements PaymentsModel
{
    public function Find($idOrWhere = false, $params = [], $limit = false) : PaymentEntity
    {
        return $this->modelToEntity(TSHModelPaymentTable::Find($idOrWhere, $params, $limit));
    }

    public function FindPage($page, $numPerPage = 20, $where = 1, $params = []) : array
    {
        return $this->modelsToEntity(TSHModelPaymentTable::FindPage($page, $numPerPage, $where, $params));
    }

    public function Save(PaymentEntity $payment)
    {
        $model = new TSHModelPaymentTable();
        $model->setFromArray([
            'payment_supplier' => $payment->supplier,
            'payment_cost_rating' => $payment->cost_rating,
            'payment_ref' => $payment->ref,
            'payment_amount' => $payment->amount,
        ]);
        $model->save();
    }


    /**
     * @param array $models
     * @return TSHModelPaymentTable[]
     */
    public function modelsToEntity(array $models) : array
    {
        $self = $this;
        return array_map(function (TSHModelPaymentTable $model) use ($self) {
            return $self->modelToEntity($model);
        }, $models);
    }

    public function modelToEntity(TSHModelPaymentTable $model) : PaymentEntity
    {
        return new PaymentEntity([
            'supplier' => $model->supplier,
            'ref' => $model->ref,
            'cost_rating' => $model->cost_rating,
            'amount' => $model->amount,
        ]);
    }
}