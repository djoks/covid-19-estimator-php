<?php

require "estimator.php";

use Pecee\SimpleRouter\SimpleRouter as Router;

Router::post("/api/v1/on-covid-19", function () {
    covid19ImpactEstimator(getInputAsJson());
});

Router::post("/api/v1/on-covid-19/xml", function () {
    return covid19ImpactEstimator(getInputAsJson());
});

Router::get("/api/v1/on-covid-19/logs", function () {
    if (!headers_sent()) header('Content-Type:text/plain');
    return file_get_contents(__DIR__ . "/../logs/estimator.log");
});

Router::all("", function () {
    return response()->header("HTTP/1.1 404 Bad Request")->json(["message" => "Route / method not found."]);
})->setMatch("//is");
