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
        $this->region = new Region($data->region);
        $this->periodType = $data->periodType;
        $this->timeToElapse = $data->timeToElapse;
        $this->reportedCases = $data->reportedCases;
        $this->population = $data->population;
        $this->totalHospitalBeds = $data->totalHospitalBeds;
    }

    function toArray()
    {
        return [
            "region" => $this->region->toArray(),
            "periodType" => (string) $this->periodType,
            "timeToElapse" => (string) $this->timeToElapse,
            "reportedCases" => (string) $this->reportedCases,
            "population" => (string) $this->population,
            "totalHospitalBeds" => (string) $this->totalHospitalBeds,
        ];
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

    function toArray()
    {
        return [
            "currentlyInfected" => (string) $this->currentlyInfected,
            "infectionsByRequestedTime" => (string) $this->infectionsByRequestedTime,
            "severeCasesByRequestedTime" => (string) $this->severeCasesByRequestedTime,
            "hospitalBedsByRequestedTime" => (string) $this->hospitalBedsByRequestedTime,
            "casesForICUByRequestedTime" => (string) $this->casesForICUByRequestedTime,
            "casesForVentilatorsByRequestedTime" => (string) $this->casesForVentilatorsByRequestedTime,
            "dollarsInFlight" => (string) $this->dollarsInFlight,
        ];
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
        $this->name = $region->name;

        $this->avgAge = $region->avgAge;

        $this->avgDailyIncomeInUSD = $region->avgDailyIncomeInUSD;

        $this->avgDailyIncomePopulation = $region->avgDailyIncomePopulation;
    }

    function toArray()
    {
        return [
            "name" => $this->name,
            "avgAge" => (string) $this->avgAge,
            "avgDailyIncomeInUSD" => (string) $this->avgDailyIncomeInUSD,
            "avgDailyIncomePopulation" => (string) $this->avgDailyIncomePopulation,
        ];
    }
}
