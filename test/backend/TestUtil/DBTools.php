<?php
/**
 * (c) Łukasz Pietrek.
 */

declare(strict_types = 1);

namespace TSH\Local\TestUtil;

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
}