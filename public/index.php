<?php
require_once '../vendor/autoload.php';
require_once '../src/EnvLoader.php';
EnvLoader::load();

// Set headers FIRST, before any output
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Content-Type: application/json');

$clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
require_once '../src/RateLimiter.php';

$rateLimiter = new RateLimiter($clientIp);

if (!$rateLimiter->isAllowed($clientIp)) {
    http_response_code(429);
    echo json_encode(['error' => 'Rate limit exceeded. Try again later.']);
    exit;
}

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$parts = explode('/', trim($requestUri, '/'));

try {
    require_once '../src/WeatherService.php';

    $city = $parts[0] ?? null;
    if (!$city) {
        http_response_code(400);
        echo json_encode(['error' => 'City not specified']);
        exit;
    }

    $service = new WeatherService();
    $weatherData = $service->getWeather($city);

    echo json_encode($weatherData);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}