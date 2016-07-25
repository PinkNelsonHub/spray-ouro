<?php

namespace Mhwk\Ouro\Client;

final class TimeSpan
{
    /**
     * @var int
     */
    private $microseconds;

    /**
     * @param int $microseconds
     */
    private function __construct(int $microseconds)
    {
        $this->microseconds = $microseconds;
    }

    /**
     * @param int $seconds
     *
     * @return TimeSpan
     */
    public static function fromSeconds(int $seconds)
    {
        return new TimeSpan($seconds * 1000 * 1000);
    }

    /**
     * @return int
     */
    public function toMicroseconds()
    {
        return $this->microseconds;
    }

    /**
     * @return int
     */
    public function toMilliseconds()
    {
        return round($this->microseconds / 1000);
    }
}
