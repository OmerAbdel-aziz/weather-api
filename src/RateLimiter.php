<?php
class RateLimiter {
    private $redis;
    private $window;
    private $maxRequests;

    public function __construct() {
        $this->redis = new Predis\Client([
            'scheme' => 'tcp',
            'host'   => $_ENV['REDIS_HOST'],
            'port'   => $_ENV['REDIS_PORT'],
        ]);
        $this->window = (int) $_ENV['RATE_LIMIT_WINDOW'];
        $this->maxRequests = (int) $_ENV['RATE_LIMIT_MAX_REQUESTS'];
    }

    public function isAllowed($clientIp) {
        $key = "rate_limit:{$clientIp}";
        $requests = $this->redis->incr($key);

        if ($requests === 1) {
            $this->redis->expire($key, $this->window);
        }

        return $requests <= $this->maxRequests;
    }
}
