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
  $currentDate = Carbon::now();
  $endDate = Carbon::parse($data["timeToElapse"] . " " . $data["periodType"]);
  $daysBetween = $currentDate->diffInDays($endDate);
  $factor = floor($daysBetween / DAYS_UNTIL_INFECTIONS_RISES);

  if ($data["periodType"] == "days" || $data["periodType"] == "day") {
    $daysBetween = $data["timeToElapse"];
  } else if ($data["periodType"] == "weeks" || $data["periodType"] == "week") {
    $daysBetween = $data["timeToElapse"] * 7;
  } else if ($data["periodType"] == "months" || $data["periodType"] == "month") {
    $daysBetween = $data["timeToElapse"] * 30;
  }

  $impact = new Impact();
  $impact->currentlyInfected = $data["reportedCases"] * IMPACT_CASES_MULTIPLIER;
  $impact->infectionsByRequestedTime = $impact->currentlyInfected * (pow(CURRENT_INFECTIONS_MULTIPLIER, $factor));
  $impact->severeCasesByRequestedTime = $impact->infectionsByRequestedTime * PERCENTAGE_CASES_BY_REQUESTED_TIME;
  $impact->hospitalBedsByRequestedTime = intval(($data["totalHospitalBeds"] * PERCENTAGE_HOSPITAL_BED_AVAILABLITY) - $impact->severeCasesByRequestedTime);
  $impact->casesForICUByRequestedTime = intval($impact->infectionsByRequestedTime * PERCENTAGE_REQUIRE_ICU_CARE);
  $impact->casesForVentilatorsByRequestedTime = intval($impact->infectionsByRequestedTime * PERCENTAGE_REQUIRE_VENTILATORS);
  $impact->dollarsInFlight = intval(($impact->infectionsByRequestedTime * $data["region"]["avgDailyIncomePopulation"] * $data["region"]["avgDailyIncomeInUSD"]) / $daysBetween);

  $severeImpact = new Impact();
  $severeImpact->currentlyInfected = $data["reportedCases"] * SEVERE_IMPACT_CASES_MULTIPLIER;
  $severeImpact->infectionsByRequestedTime = $severeImpact->currentlyInfected * (pow(CURRENT_INFECTIONS_MULTIPLIER, $factor));
  $severeImpact->severeCasesByRequestedTime = $severeImpact->infectionsByRequestedTime * PERCENTAGE_CASES_BY_REQUESTED_TIME;
  $severeImpact->hospitalBedsByRequestedTime = intval(($data["totalHospitalBeds"] * PERCENTAGE_HOSPITAL_BED_AVAILABLITY) - $severeImpact->severeCasesByRequestedTime);
  $severeImpact->casesForICUByRequestedTime = intval($severeImpact->infectionsByRequestedTime * PERCENTAGE_REQUIRE_ICU_CARE);
  $severeImpact->casesForVentilatorsByRequestedTime = intval($severeImpact->infectionsByRequestedTime * PERCENTAGE_REQUIRE_VENTILATORS);
  $severeImpact->dollarsInFlight = intval(($severeImpact->infectionsByRequestedTime * $data["region"]["avgDailyIncomePopulation"] * $data["region"]["avgDailyIncomeInUSD"]) / $daysBetween);

  $data = new Data($data);

  if (stripos(request()->getUrl(), "/xml") !== false) {
    return ArrayToXml::convert([
      "data" => $data->toStringArray(),
      "impact" => $impact->toStringArray(),
      "severeImpact" => $severeImpact->toStringArray()
    ], "covid19estimator");
  } else {
    return [
      "data" => $data->toJsonArray(),
      "impact" => $impact->toJsonArray(),
      "severeImpact" => $severeImpact->toJsonArray()
    ];
  }
}
