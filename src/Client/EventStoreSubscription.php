<?php

namespace Mhwk\Ouro\Client;

abstract class EventStoreSubscription
{
    /**
     * @var string
     */
    private $streamId;

    /**
     * @var int
     */
    private $lastCommitPosition;

    /**
     * @var int
     */
    private $lastEventNumber;

    /**
     * @param string $streamId
     * @param int $lastCommitPosition
     * @param int $lastEventNumber
     */
    protected function __construct(string $streamId, int $lastCommitPosition, int $lastEventNumber)
    {
        $this->streamId = $streamId;
        $this->lastCommitPosition = $lastCommitPosition;
        $this->lastEventNumber = $lastEventNumber;
    }

    /**
     * @return void
     */
    function dispose()
    {
        $this->unsubscribe();
    }

    /**
     * @return void
     */
    function close()
    {
        $this->unsubscribe();
    }

    /**
     * @return void
     */
    abstract function unsubscribe();
}
