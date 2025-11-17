<?php
require_once '../vendor/autoload.php';
require_once '../src/EnvLoader.php';
EnvLoader::load();


$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$parts = explode('/', trim($requestUri, '/'));

try {
    require_once '../src/WeatherService.php';

     // get city from URL (e.g. /london) or query param
    $city = $parts[0] ?? null;
    if (!$city) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'City not specified']);
        exit;
    }
   

    $service  = new WeatherService();
    $weatherData = $service->getWeather($city);


    header('Content-Type: application/json');
    echo json_encode($weatherData);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

