<?php
require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Load classes
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/App/models/trip.php';
require_once __DIR__ . '/App/models/country.php';
require_once __DIR__ . '/App/controllers/TripController.php';
require_once __DIR__ . '/App/controllers/CountryController.php';
require_once __DIR__ . '/core/Router.php';

// CORS headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Database connection
$database = new Database();
$db = $database->getConnection();

// Parse URL
$requestUri = str_replace('/Orizon/', '', $_SERVER['REQUEST_URI']);
$uriParts   = explode('/', explode('?', $requestUri)[0]);

$resource = $uriParts[0] ?? '';
$id       = $uriParts[1] ?? null;
$method   = $_SERVER['REQUEST_METHOD'];

// Start the Router
$router = new Router($db);
$router->dispatch($resource, $id, $method);