<?php

class Data
{
    public $region;

    public $periodType;

    public $timeToElapse;

    public $reportedCases;

    public $population;

    public $totalHospitalBeds;

    function __construct($data)
    {
        $this->region = new Region($data["region"]);
        $this->periodType = $data["periodType"];
        $this->timeToElapse = $data["timeToElapse"];
        $this->reportedCases = $data["reportedCases"];
        $this->population = $data["population"];
        $this->totalHospitalBeds = $data["totalHospitalBeds"];
    }

    function toStringArray()
    {
        return array(
            "region" => $this->region->toStringArray(),
            "periodType" => (string) $this->periodType,
            "timeToElapse" => (string) $this->timeToElapse,
            "reportedCases" => (string) $this->reportedCases,
            "population" => (string) $this->population,
            "totalHospitalBeds" => (string) $this->totalHospitalBeds,
        );
    }

    function toJsonArray()
    {
        return array(
            "region" => $this->region->toJsonArray(),
            "periodType" => $this->periodType,
            "timeToElapse" =>  $this->timeToElapse,
            "reportedCases" => $this->reportedCases,
            "population" => $this->population,
            "totalHospitalBeds" => $this->totalHospitalBeds,
        );
    }
}



class Impact
{
    public $currentlyInfected;

    public $infectionsByRequestedTime;

    public $severeCasesByRequestedTime;

    public $hospitalBedsByRequestedTime;

    public $casesForICUByRequestedTime;

    public $casesForVentilatorsByRequestedTime;

    public $dollarsInFlight;

    function toStringArray()
    {
        return array(
            "currentlyInfected" => (string) $this->currentlyInfected,
            "infectionsByRequestedTime" => (string) $this->infectionsByRequestedTime,
            "severeCasesByRequestedTime" => (string) $this->severeCasesByRequestedTime,
            "hospitalBedsByRequestedTime" => (string) $this->hospitalBedsByRequestedTime,
            "casesForICUByRequestedTime" => (string) $this->casesForICUByRequestedTime,
            "casesForVentilatorsByRequestedTime" => (string) $this->casesForVentilatorsByRequestedTime,
            "dollarsInFlight" => (string) $this->dollarsInFlight,
        );
    }

    function toJsonArray()
    {
        return array(
            "currentlyInfected" => $this->currentlyInfected,
            "infectionsByRequestedTime" => $this->infectionsByRequestedTime,
            "severeCasesByRequestedTime" => $this->severeCasesByRequestedTime,
            "hospitalBedsByRequestedTime" => $this->hospitalBedsByRequestedTime,
            "casesForICUByRequestedTime" => $this->casesForICUByRequestedTime,
            "casesForVentilatorsByRequestedTime" => $this->casesForVentilatorsByRequestedTime,
            "dollarsInFlight" => $this->dollarsInFlight,
        );
    }
}




class Region
{
    public $name;

    public $avgAge;

    public $avgDailyIncomeInUSD;

    public $avgDailyIncomePopulation;

    function __construct($region)
    {
        $this->name = $region["name"];

        $this->avgAge = $region["avgAge"];

        $this->avgDailyIncomeInUSD = $region["avgDailyIncomeInUSD"];

        $this->avgDailyIncomePopulation = $region["avgDailyIncomePopulation"];
    }

    function toStringArray()
    {
        return array(
            "name" => $this->name,
            "avgAge" => (string) $this->avgAge,
            "avgDailyIncomeInUSD" => (string) $this->avgDailyIncomeInUSD,
            "avgDailyIncomePopulation" => (string) $this->avgDailyIncomePopulation,
        );
    }

    function toJsonArray()
    {
        return array(
            "name" => $this->name,
            "avgAge" => $this->avgAge,
            "avgDailyIncomeInUSD" => $this->avgDailyIncomeInUSD,
            "avgDailyIncomePopulation" => $this->avgDailyIncomePopulation,
        );
    }
}
