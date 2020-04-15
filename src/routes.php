<?php

require "estimator.php";

use Pecee\SimpleRouter\SimpleRouter as Router;

Router::post("/api/v1/on-covid-19", function () {
    $response = covid19ImpactEstimator(input()->all());
    header('Content-Type: application/json');
    return json_encode($response);
});

Router::post("/api/v1/on-covid-19/json", function () {
    $response = covid19ImpactEstimator(input()->all());
    header('Content-Type: application/json');
    return json_encode($response);
});

Router::post("/api/v1/on-covid-19/xml", function () {
    $response = covid19ImpactEstimator(input()->all());
    header('Content-Type: application/xml');
    return $response;
});

Router::get("/api/v1/on-covid-19/logs", function () {
    header('Content-Type: text/plain');
    return file_get_contents(__DIR__ . "/../logs/estimator.log");
});

Router::all("", function () {
    http_response_code(404);
    return json_encode(["message" => "Route / method not found."]);
})->setMatch("//is");
