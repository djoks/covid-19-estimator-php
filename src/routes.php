<?php

require "estimator.php";

use Pecee\SimpleRouter\SimpleRouter as Router;

Router::post("/api/v1/on-covid-19", function () {
    return covid19ImpactEstimator(getInputAsJson());
});

Router::post("/api/v1/on-covid-19/xml", function () {
    return covid19ImpactEstimator(getInputAsJson());
});

Router::get("/api/v1/on-covid-19/logs", function () {
    return file_get_contents(__DIR__ . "/../logs/estimator.log");
});

Router::all("", function () {
    return json_encode(["message" => "Route / method not found."]);
})->setMatch("//is");
