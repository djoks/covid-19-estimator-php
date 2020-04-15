<?php

require "vendor/autoload.php";
require "src/helpers.php";
require "objects.php";

use Carbon\Carbon;
use Spatie\ArrayToXml\ArrayToXml;

define("DAYS_UNTIL_INFECTIONS_RISES", 3);
define("CURRENT_INFECTIONS_MULTIPLIER", 2);
define("IMPACT_CASES_MULTIPLIER", 10);
define("SEVERE_IMPACT_CASES_MULTIPLIER", 50);
define("PERCENTAGE_CASES_BY_REQUESTED_TIME", (15 / 100));
define("PERCENTAGE_HOSPITAL_BED_AVAILABLITY", (35 / 100));
define("PERCENTAGE_REQUIRE_ICU_CARE", (5 / 100));
define("PERCENTAGE_REQUIRE_VENTILATORS", (2 / 100));

function covid19ImpactEstimator($data)
{
  $errors = validate($data);

  if (!empty($errors)) {
    http_response_code(400);
    return response()->json([
      "errors" => $errors
    ]);
  }

  $currentDate = Carbon::now();
  $endDate = Carbon::parse($data->timeToElapse . " " . $data->periodType);
  $daysBetween = $currentDate->diffInDays($endDate);
  $factor = floor($daysBetween / DAYS_UNTIL_INFECTIONS_RISES);

  $impact = new Impact();
  $impact->currentlyInfected = $data->reportedCases * IMPACT_CASES_MULTIPLIER;
  $impact->infectionsByRequestedTime = $impact->currentlyInfected * (pow(CURRENT_INFECTIONS_MULTIPLIER, $factor));
  $impact->severeCasesByRequestedTime = $impact->infectionsByRequestedTime * PERCENTAGE_CASES_BY_REQUESTED_TIME;
  $impact->hospitalBedsByRequestedTime = ($data->totalHospitalBeds * PERCENTAGE_HOSPITAL_BED_AVAILABLITY) - $impact->severeCasesByRequestedTime;
  $impact->casesForICUByRequestedTime = $impact->infectionsByRequestedTime * PERCENTAGE_REQUIRE_ICU_CARE;
  $impact->casesForVentilatorsByRequestedTime = $impact->infectionsByRequestedTime * PERCENTAGE_REQUIRE_VENTILATORS;
  $impact->dollarsInFlight = ($impact->infectionsByRequestedTime * $data->region->avgDailyIncomePopulation) * $data->region->avgDailyIncomeInUSD * $daysBetween;

  $severeImpact = new Impact();
  $severeImpact->currentlyInfected = $data->reportedCases * SEVERE_IMPACT_CASES_MULTIPLIER;
  $severeImpact->infectionsByRequestedTime = $severeImpact->currentlyInfected * (pow(CURRENT_INFECTIONS_MULTIPLIER, $factor));
  $severeImpact->severeCasesByRequestedTime = $severeImpact->infectionsByRequestedTime * PERCENTAGE_CASES_BY_REQUESTED_TIME;
  $severeImpact->hospitalBedsByRequestedTime = ($data->totalHospitalBeds * PERCENTAGE_HOSPITAL_BED_AVAILABLITY) - $impact->severeCasesByRequestedTime;
  $severeImpact->casesForICUByRequestedTime = $severeImpact->infectionsByRequestedTime * PERCENTAGE_REQUIRE_ICU_CARE;
  $severeImpact->casesForVentilatorsByRequestedTime = $severeImpact->infectionsByRequestedTime * PERCENTAGE_REQUIRE_VENTILATORS;
  $severeImpact->dollarsInFlight = ($severeImpact->infectionsByRequestedTime * $data->region->avgDailyIncomePopulation) * $data->region->avgDailyIncomeInUSD * $daysBetween;

  logRequest();

  if (stripos(request()->getUrl(), "/xml") !== false) {
    $data = new Data($data);
    if (!headers_sent()) header('Content-Type:application/xml');
    return ArrayToXml::convert([
      "data" => $data->toArray(),
      "impact" => $impact->toArray(),
      "severeImpact" => $severeImpact->toArray()
    ], "covid19estimator");
  } else {
    return response()->json([
      "data" => $data,
      "impact" => $impact,
      "severeImpact" => $severeImpact
    ]);
  }
}
