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
    if (!headers_sent()) header('X-PHP-Response-Code: 404', true, 404);
    return response()->json(["message" => "Route / method not found."]);
})->setMatch("//is");
