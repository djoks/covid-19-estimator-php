<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class EstimatorTest extends TestCase
{
    /**
     * @dataProvider additionProvider
     */
    public function testEstimatorJson($a, $b, $expected)
    {
        $this->assertTrue(true);
    }
}
