<?php
/**
 * (c) Łukasz Pietrek.
 */

declare(strict_types = 1);

namespace TSH\Local\TestUtil;

use Prophecy\Prophet;
use TSH\Local\MySqlPaymentsModel;
use TSH\Local\PaymentEntity;

trait DBTools
{
    private function credentials() : string
    {
        return sprintf(
            'mysql --host="%s" --user="%s" --password="%s" --database="%s" ',
            DB_HOST,
            DB_USERNAME,
            DB_PASSWORD,
            DB_NAME
        );
    }

    public function resetData()
    {
        $this->removeData();
        $this->insertData();
    }

    public function removeData()
    {
        exec($this->credentials() . ' --execute="DROP TABLE IF EXISTS \`payments\`"');
    }

    public function clearData()
    {
        exec($this->credentials() . ' --execute="TRUNCATE TABLE \`payments\`"');
    }

    public function insertData()
    {
        $script = __DIR__ . '/../../../specification/data/payments.sql';
        exec($this->credentials() . ' < ' . $script);
    }

    /**
     * @param array $payments
     */
    public function insertCustomPayments(array $payments = [])
    {
        $model = new MySqlPaymentsModel();
        foreach($payments as $payment) {
            $model->Save(new PaymentEntity($payment));
        }
    }

    /**
     * Helper for Mocking TSH_Model::Find()
     *
     * Last parameter have to contain array of rows db should return to model
     *
     * Other params have to contain data passed to TSH_Db::selectFirst()
     * called by TSH_Model
     *
     * @param $sql
     * @param $params
     * @param $willReturn
     */
    public function MockFind($sql, $params, $willReturn)
    {
        $prophet = (new Prophet)->prophesize(DBMock::class);
        $prophet->selectFirst($sql, $params)->WillReturn($willReturn);
        $stub = $prophet->reveal();
        DBMock::Get()->mockWithMe($stub);
    }

    /**
     * Helper for Mocking TSH_Model::FindPage()
     *
     * Last parameter have to contain array of rows db should return to model
     *
     * Other params have to contain data passed to TSH_Db::selectPage()
     * called by TSH_Model
     *
     * @param $page
     * @param $sql
     * @param $params
     * @param $numPerPage
     * @param $willReturn
     */
    public function MockFindPage($page, $sql, $params, $numPerPage, $willReturn)
    {
        $prophet = (new Prophet)->prophesize(DBMock::class);
        $prophet->selectPage($page, $sql, $params, $numPerPage)->WillReturn($willReturn);
        $stub = $prophet->reveal();
        DBMock::Get()->mockWithMe($stub);
    }
}