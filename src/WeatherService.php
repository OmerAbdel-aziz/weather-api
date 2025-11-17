<?php


//load environment variables
require_once 'EnvLoader.php';
EnvLoader::load();


//define WeatherService class
class WeatherService {
    private $apiKey;
    private $cacheExpiration;
    


    public function __construct(){
        $this->apiKey = $_ENV['VISUAL_CROSSING_API_KEY'] ?? '';
        $this->cacheExpiration = intval($_ENV['CACHE_EXPIRATION'] ?? 43200);
    }

    public function getWeather($city) {
        $url = "https://weather.visualcrossing.com/VisualCrossingWebServices/rest/services/timeline/{$city}"
         . "?unitGroup=metric&key={$this->apiKey}&contentType=json";

        //phase1 : curl session  1-initilize, 2-set options, 3-execute, 4-get info, 5-close
         //initialize cURL session
         $ch = curl_init();


         //set cURL options
         curl_setopt_array($ch, [
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => true,   // Return response as string instead of outputting it
                CURLOPT_TIMEOUT        => 10,     // Max 10 seconds waiting for the API
                CURLOPT_HTTPHEADER     => ['User-Agent: MyWeatherAPI/1.0'] // Some APIs block requests without User-Agent
            ]);


        //execute cURL request
        $response = curl_exec($ch);

        //get HTTP response code
        $http_code = curl_getinfo($ch , CURLINFO_HTTP_CODE);

        //free cURL resources
        curl_close($ch);

        

        //phase2 : handle response
        if($http_code != 200){
            throw new Exception('Error fetching weather data: HTTP ' . $http_code);
        }

        //convert JSON response to PHP array
        $data = json_decode($response, true);


        //check for JSON decoding errors
        if(json_last_error() !== JSON_ERROR_NONE){
            throw new Exception('Error decoding weather data: ' . json_last_error_msg());
        }

        //extract relevant weather information
        $current = $data['currentConditions'] ?? [];
        if(empty($current)){
            throw new Exception('No current weather data available');
        }

        return [
            'location' => $city,
            'temperature' => $current['temp'],
            'condition' => $current['conditions'],
            'humidity' => $current['humidity'],
            'wind_speed' => $current['windspeed']
        ];

    }
}