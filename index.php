<?php

declare(strict_types=1);

namespace App;

require_once realpath(__DIR__ . "/vendor/autoload.php");
require_once("../Server2/vendor/autoload.php");
spl_autoload_register(function ($class) {
    require __DIR__ . "/src/$class.php";
});

/* Use Dotenv to use environment variables */

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

/* Set Header to allow acces -> Solve CORS */
/* header('Content-Type: application/json'); */
header('Access-Control-Allow-Origin: http://localhost:5173'); // CORS
header('Access-Control-Allow-Methods: *, OPTIONS');
header('Access-Control-Allow-Headers: *');
header("Content-type: application/json; charset=UTF-8");
/* Awnser to axios -> solve CORS */
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

/* Set error and exception handler */
set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

/* Split the URL($_SERVER["REQUEST_URI"]) in parts split by "/" and fill some variables as Params for the controller */
$parts = explode("/", $_SERVER["REQUEST_URI"]);

/* if third part of the URL is verify -> get the datas, else set query_params to "" */
if ($parts[2] === "verify") {
    $query_str = parse_url($_SERVER["REQUEST_URI"], PHP_URL_QUERY);
    parse_str($query_str, $query_params);
    $query_params = $query_params["verify"];
} else {
    $query_params = "";
}

/* parts 2 -> route like user, login etc */
$route = $parts[2];

/* parts 3 -> possible ID, else set to NULL */
$id = $parts[3] ?? null;

/* parts 3 -> possible permission, else set to NULL */
if (isset($parts[4])) {
    $permission = $parts[4];
} else {
    $permission = "";
}

/* Connect to the Database -> Create new DB by calling the Class and filling the Constructor */
$database = new Database($_ENV["DB_HOST"], $_ENV["DB_DATABSE"], $_ENV["DB_USER"], $_ENV["DB_PW"]);

/* new Gatway with db as arg */
$gateway = new UserGateway($database);

/* controller with gateway as arg */
$controller = new UserController($gateway);

/* controller get the parts from the URL as Params */
$controller->processRequest($_SERVER["REQUEST_METHOD"], $id, $route, $query_params, $permission);
