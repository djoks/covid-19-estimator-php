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
        $client = new Client();
        $response = $client->request("POST", "https://a79fdd0d.ngrok.io/api/v1/on-covid-19", [
            "json" => [
                "region" => [
                    "name" => "Africa",
                    "avgAge" => 19.7,
                    "avgDailyIncomeInUSD" => 5,
                    "avgDailyIncomePopulation" => 0.71
                ],
                "periodType" => "days",
                "timeToElapse" => 58,
                "reportedCases" => 674,
                "population" => 66622705,
                "totalHospitalBeds" => 1380614
            ],
            "headers" => [
                "Accept" => "application/json"
            ]
        ]);
    }
}
