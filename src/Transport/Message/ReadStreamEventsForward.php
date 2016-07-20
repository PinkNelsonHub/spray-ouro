<?php

namespace Mhwk\Ouro\Transport\Message;

use Mhwk\Ouro\Transport\Message\UserCredentials;

final class ReadStreamEventsForward
{
    /**
     * @var string
     */
    private $stream;

    /**
     * @var int
     */
    private $start;

    /**
     * @var int
     */
    private $count;

    /**
     * @var bool
     */
    private $resolveLinkTos;

    /**
     * @param string $stream
     * @param int $start
     * @param int $count
     * @param bool $resolveLinkTos
     */
    public function __construct(string $stream, int $start, int $count, bool $resolveLinkTos)
    {
        $this->stream = $stream;
        $this->start = $start;
        $this->count = $count;
        $this->resolveLinkTos = $resolveLinkTos;
    }

    /**
     * @return string
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * @return int
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @return boolean
     */
    public function isResolveLinkTos()
    {
        return $this->resolveLinkTos;
    }
}