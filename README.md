# Weather API

A lightweight PHP-based weather API that fetches real-time weather data and implements rate limiting with Redis caching.

## Features

- **Real-time Weather Data**: Fetches current weather conditions from Visual Crossing API
- **Caching**: Redis-based caching to reduce API calls and improve response times
- **Rate Limiting**: IP-based rate limiting to prevent API abuse
- **CORS Support**: Cross-Origin Resource Sharing enabled for web clients
- **Error Handling**: Comprehensive error handling with meaningful HTTP status codes

## Requirements

- PHP 7.4+
- Redis server
- Composer
- cURL extension for PHP

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd weather-app
```

2. Install dependencies:
```bash
composer install
```

3. Set up environment variables:
Create a `.env` file in the project root with the following variables:
```env
VISUAL_CROSSING_API_KEY=your_api_key_here
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_DB=0
CACHE_EXPIRATION=43200
RATE_LIMIT_REQUESTS=100
RATE_LIMIT_WINDOW=3600
```

4. Start Redis server:
```bash
redis-server
```

Or if installed as a service:
```bash
sudo systemctl start redis-server
```

## Usage

### Starting the Development Server

```bash
php -S localhost:8000 -t public
```

### API Endpoints

#### Get Weather for a City

**Request:**
```
GET http://localhost:8000/weather/{city}
```

**Example:**
```bash
curl http://localhost:8000/weather/London
```

**Response (200 OK):**
```json
{
  "location": "London",
  "temperature": 12.5,
  "condition": "Clear",
  "humidity": 65,
  "wind_speed": 8.3
}
```

**Error Responses:**

- **400 Bad Request**: City not specified
```json
{
  "error": "City not specified"
}
```

- **429 Too Many Requests**: Rate limit exceeded
```json
{
  "error": "Rate limit exceeded. Try again later."
}
```

- **500 Internal Server Error**: API or server error
```json
{
  "error": "Error message describing the issue"
}
```

## Project Structure

```
weather-app/
├── public/
│   └── index.php           # Main entry point
├── src/
│   ├── EnvLoader.php       # Environment variable loader
│   ├── WeatherService.php  # Weather API integration
│   └── RateLimiter.php     # Rate limiting logic
├── vendor/                 # Composer dependencies
├── composer.json
└── README.md
```

## Configuration

### Environment Variables

- `VISUAL_CROSSING_API_KEY`: Your Visual Crossing Weather API key (required)
- `REDIS_HOST`: Redis server hostname (default: 127.0.0.1)
- `REDIS_PORT`: Redis server port (default: 6379)
- `REDIS_DB`: Redis database number (default: 0)
- `CACHE_EXPIRATION`: Cache expiration time in seconds (default: 43200 - 12 hours)
- `RATE_LIMIT_REQUESTS`: Max requests per window (default: 100)
- `RATE_LIMIT_WINDOW`: Rate limit window in seconds (default: 3600 - 1 hour)

## Getting an API Key

1. Visit [Visual Crossing Weather](https://www.visualcrossing.com/weather/weather-data-services)
2. Sign up for a free account
3. Navigate to your account settings and copy your API key
4. Add it to your `.env` file

## Architecture

### WeatherService
Handles all weather data fetching logic:
- Caches weather data in Redis to reduce API calls
- Fetches data from Visual Crossing API via cURL
- Extracts relevant weather information (temperature, conditions, humidity, wind speed)
- Handles API errors and JSON parsing

### RateLimiter
Implements IP-based rate limiting:
- Tracks requests per IP address using Redis
- Prevents abuse by limiting requests within a time window
- Returns 429 status when limit is exceeded

### EnvLoader
Loads environment variables from `.env` file for secure configuration management

## Development

### Testing the API

```bash
# Test with curl
curl http://localhost:8000/weather/Tokyo

# Check rate limiting
for i in {1..101}; do curl http://localhost:8000/weather/London; done
```

### Troubleshooting

**Redis Connection Error:**
- Ensure Redis is running: `redis-cli ping` (should return PONG)
- Check Redis host and port in `.env`

**401 Unauthorized:**
- Verify `VISUAL_CROSSING_API_KEY` is correct
- Ensure the API key has not expired or been revoked

**Cannot modify header information warning:**
- Ensure no output is sent before headers
- Check for BOM (Byte Order Mark) in PHP files

## Dependencies

- **Predis**: PHP Redis client for caching and rate limiting
- **PSR HTTP Message**: HTTP message interfaces (dependency of Predis)

## License

This project is licensed under the MIT License.

## Contributing

Feel free to submit issues and enhancement requests!
