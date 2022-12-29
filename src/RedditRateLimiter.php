<?php

namespace CodeWizz\RedditAPI;

class RedditRateLimiter
{
    private $enabled;
    private $interval;
    private $last_request;

    public function __construct($enabled = true, $interval = 2)
    {
        $this->enabled = $enabled;
        $this->interval = $interval;
        $this->last_request = microtime(true) * 10000;
    }

    /**
     * Enable the rate limiter, on by default.
     */
    public function enable(): void
    {
        $this->enabled = true;
    }

    /**
     * Disable the rate limiter.
     * This is meant to allow you to perform requests in bursts, but me mindful of reddit's rate limits and your program's structure.
     * https://github.com/reddit/reddit/wiki/API.
     */
    public function disable(): void
    {
        $this->enabled = false;
    }

    /**
     * Set the rate limiter to wait the specified number of seconds past the previous API call to make the next one.
     * If this time has already elapsed during execution of other parts of the program, no wait is needed.
     *
     * @param int|float $interval Number of seconds that must elapse between each API call.
     */
    public function setInterval($interval): void
    {
        $this->interval = $interval;
    }

    /**
     * Used by Phapper object to wait until another API call can be made.
     */
    public function wait(): void
    {
        $now = microtime(true) * 10000;
        $wait_until = $this->last_request + ($this->interval * 10000);
        if ($this->enabled && $now < $wait_until) {
            usleep((int)($wait_until - $now) * 100);
        }
        $this->last_request = microtime(true) * 10000;
    }
}
