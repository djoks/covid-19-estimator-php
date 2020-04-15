<?php
ob_start();

use Pecee\SimpleRouter\SimpleRouter as Router;

require_once "src/routes.php";

Router::start();

ob_end_flush();
