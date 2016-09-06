<?php
/**
 * (c) Łukasz Pietrek.
 */

declare(strict_types = 1);


namespace TSH\Local\Test;


use PHPUnit\Framework\TestCase;
use TSH\Local\PaymentsRequest;

class PaymentsRequestTest extends TestCase
{
    /** @test */
    public function requestModelFiltersOutDataToPreventXssAttacks()
    {
        $request = new PaymentsRequest(1, '"><script>alert("jaka_piekna_wtopa")</script><"', 3);
        self::assertSame(
            '&#34;&#62;&#60;script&#62;alert(&#34;jaka_piekna_wtopa&#34;)&#60;/script&#62;&#60;&#34;',
            $request->supplier()
        );
    }
}
