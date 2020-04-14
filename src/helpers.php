<?php

use Carbon\Carbon;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pecee\SimpleRouter\SimpleRouter as Router;
use Pecee\Http\Url;
use Pecee\Http\Response;
use Pecee\Http\Request;

/**
 * Get url for a route by using either name/alias, class or method name.
 *
 * The name parameter supports the following values:
 * - Route name
 * - Controller/resource name (with or without method)
 * - Controller class name
 *
 * When searching for controller/resource by name, you can use this syntax "route.name@method".
 * You can also use the same syntax when searching for a specific controller-class "MyController@home".
 * If no arguments is specified, it will return the url for the current loaded route.
 *
 * @param string|null $name
 * @param string|array|null $parameters
 * @param array|null $getParams
 * @return \Pecee\Http\Url
 * @throws \InvalidArgumentException
 */
function url(?string $name = null, $parameters = null, ?array $getParams = null): Url
{
    return Router::getUrl($name, $parameters, $getParams);
}

/**
 * @return \Pecee\Http\Response
 */
function response(): Response
{
    return Router::response();
}

/**
 * @return \Pecee\Http\Request
 */
function request(): Request
{
    return Router::request();
}

/**
 * Get input class
 * @param string|null $index Parameter index name
 * @param string|null $defaultValue Default return value
 * @param array ...$methods Default methods
 * @return \Pecee\Http\Input\InputHandler|array|string|null
 */
function input($index = null, $defaultValue = null, ...$methods)
{
    if ($index !== null) {
        return request()->getInputHandler()->value($index, $defaultValue, ...$methods);
    }

    return request()->getInputHandler();
}

/**
 * Get input class as json
 * @param string|null $index Parameter index name
 * @param string|null $defaultValue Default return value
 * @param array ...$methods Default methods
 * @return \Pecee\Http\Input\InputHandler|array|string|null
 */
function getInputAsJson($index = null, $defaultValue = null, ...$methods)
{
    return json_decode(json_encode(input($index = null, $defaultValue = null, ...$methods)->all()));
}


/**
 * @param string $url
 * @param int|null $code
 */
function redirect(string $url, ?int $code = null): void
{
    if ($code !== null) {
        response()->httpCode($code);
    }

    response()->redirect($url);
}

/**
 * Get current csrf-token
 * @return string|null
 */
function csrf_token(): ?string
{
    $baseVerifier = Router::router()->getCsrfVerifier();
    if ($baseVerifier !== null) {
        return $baseVerifier->getTokenProvider()->getToken();
    }

    return null;
}

/**
 * Validate incoming request body
 * @return array
 */
function validate($data)
{
    $errors = [];
    $region = [];
    $periodType = [];
    $timeToElapse = [];
    $reportedCases = [];
    $population = [];
    $totalHospitalBeds = [];

    if (isset($data->region)) {
        if (is_object($data->region)) {
            if (!is_string($data->region->name)) {
                array_push($region, "Name must be a vlid string.");
            }

            if (!is_numeric($data->region->avgAge)) {
                array_push($region, "avgAge must be numeric.");
            }

            if (!is_numeric($data->region->avgDailyIncomeInUSD)) {
                array_push($region, "avgDailyIncomeInUSD must be numeric.");
            }

            if (!is_numeric($data->region->avgDailyIncomePopulation)) {
                array_push($region, "avgDailyIncomePopulation must be numeric.");
            }
        } else {
            array_push($region, "Region must be a valid json object.");
        }
    } else {
        array_push($region, "The region field is required.");
    }

    if (isset($data->periodType)) {
        if (!in_array($data->periodType, ["day", "days", "week", "weeks", "month", "months"])) {
            array_push($periodType, "The periodType field should be either days, weeks or months.");
        }
    } else {
        array_push($region, "The periodType field is required.");
    }

    if (isset($data->timeToElapse)) {
        if (is_numeric($data->timeToElapse)) {
            if ($data->timeToElapse < 1) array_push($timeToElapse, "The timeToElapse field must be greater than 0.");
        } else {
            array_push($timeToElapse, "The timeToElapse field must be numeric.");
        }
    } else {
        array_push($timeToElapse, "The periodType field is required.");
    }

    if (isset($data->reportedCases)) {
        if (is_numeric($data->reportedCases)) {
            if ($data->reportedCases < 1) array_push($reportedCases, "The reportedCases field must be greater than 0.");
        } else {
            array_push($reportedCases, "The reportedCases field must be numeric.");
        }
    } else {
        array_push($reportedCases, "The reportedCases field is required.");
    }

    if (isset($data->population)) {
        if (is_numeric($data->population)) {
            if ($data->population < 1) array_push($population, "The population field must be greater than 0.");
        } else {
            array_push($population, "The population field must be numeric.");
        }
    } else {
        array_push($population, "The population field is required.");
    }

    if (isset($data->totalHospitalBeds)) {
        if (is_numeric($data->totalHospitalBeds)) {
            if ($data->totalHospitalBeds < 0) array_push($totalHospitalBeds, "The totalHospitalBeds field must be greater than or equal to 0.");
        } else {
            array_push($totalHospitalBeds, "The totalHospitalBeds field must be numeric.");
        }
    } else {
        array_push($totalHospitalBeds, "The totalHospitalBeds field is required.");
    }

    if (!empty($region)) array_push($errors, ["region" => $region]);
    if (!empty($periodType)) array_push($errors, ["periodType" => $periodType]);
    if (!empty($timeToElapse)) array_push($errors, ["timeToElapse" => $timeToElapse]);
    if (!empty($reportedCases)) array_push($errors, ["reportedCases" => $reportedCases]);
    if (!empty($population)) array_push($errors, ["population" => $population]);
    if (!empty($totalHospitalBeds)) array_push($errors, ["totalHospitalBeds" => $totalHospitalBeds]);

    return $errors;
}

/**
 * Log request execution time to file
 */
function logRequest()
{
    // the default date format is "Y-m-d\TH:i:sP"
    $dateFormat = "Y-m-d\TH:i:sP";
    // the default output format is "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n"
    $output = "%message%\n";
    // finally, create a formatter
    $formatter = new LineFormatter($output, $dateFormat);

    // Create a handler
    $stream = new StreamHandler("logs/estimator.log", Logger::DEBUG);
    $stream->setFormatter($formatter);

    $log = new Logger("Estimator");
    $log->pushHandler($stream);

    $log->info(Carbon::now()->timestamp . "\t\t" . request()->getUrl() . "\t\tDone in " . number_format(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"], 2) . " seconds\n\n");
}
