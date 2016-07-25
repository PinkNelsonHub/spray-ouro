<?php

namespace Mhwk\Ouro\Transport\Message;

final class CreatePersistentSubscription
{
    /**
     * @var string
     */
    private $subscriptionGroupName;

    /**
     * @var string
     */
    private $eventStreamId;

    /**
     * @var bool
     */
    private $resolveLinkTos;

    /**
     * @var int
     */
    private $startFrom;

    /**
     * @var int
     */
    private $messageTimeoutMilliseconds;

    /**
     * @var bool
     */
    private $recordStatistics;

    /**
     * @var int
     */
    private $liveBufferSize;

    /**
     * @var int
     */
    private $readBatchSize;

    /**
     * @var int
     */
    private $bufferSize;

    /**
     * @var int
     */
    private $maxRetryCount;

    /**
     * @var bool
     */
    private $preferRoundRobin;

    /**
     * @var int
     */
    private $checkPointAfterTime;

    /**
     * @var int
     */
    private $checkPointMaxCount;

    /**
     * @var int
     */
    private $checkPointMinCount;

    /**
     * @var int
     */
    private $subscriberMaxCount;

    /**
     * @var string
     */
    private $namedConsumerStrategy;

    /**
     * @param string $subscriptionGroupName
     * @param string $eventStreamId
     * @param bool $resolveLinkTos
     * @param int $startFrom
     * @param int $messageTimeoutMilliseconds
     * @param bool $recordStatistics
     * @param int $liveBufferSize
     * @param int $readBatchSize
     * @param int $bufferSize
     * @param int $maxRetryCount
     * @param bool $preferRoundRobin
     * @param int $checkPointAfterTime
     * @param int $checkPointMaxCount
     * @param int $checkPointMinCount
     * @param int $subscriberMaxCount
     * @param string $namedConsumerStrategy
     */
    public function __construct(
        string $subscriptionGroupName,
        string $eventStreamId,
        bool $resolveLinkTos,
        int $startFrom,
        int $messageTimeoutMilliseconds,
        bool $recordStatistics,
        int $liveBufferSize,
        int $readBatchSize,
        int $bufferSize,
        int $maxRetryCount,
        bool $preferRoundRobin,
        int $checkPointAfterTime,
        int $checkPointMaxCount,
        int $checkPointMinCount,
        int $subscriberMaxCount,
        string $namedConsumerStrategy)
    {
        $this->subscriptionGroupName = $subscriptionGroupName;
        $this->eventStreamId = $eventStreamId;
        $this->resolveLinkTos = $resolveLinkTos;
        $this->startFrom = $startFrom;
        $this->messageTimeoutMilliseconds = $messageTimeoutMilliseconds;
        $this->recordStatistics = $recordStatistics;
        $this->liveBufferSize = $liveBufferSize;
        $this->readBatchSize = $readBatchSize;
        $this->bufferSize = $bufferSize;
        $this->maxRetryCount = $maxRetryCount;
        $this->preferRoundRobin = $preferRoundRobin;
        $this->checkPointAfterTime = $checkPointAfterTime;
        $this->checkPointMaxCount = $checkPointMaxCount;
        $this->checkPointMinCount = $checkPointMinCount;
        $this->subscriberMaxCount = $subscriberMaxCount;
        $this->namedConsumerStrategy = $namedConsumerStrategy;
    }

    /**
     * @return string
     */
    public function getSubscriptionGroupName(): string
    {
        return $this->subscriptionGroupName;
    }

    /**
     * @return string
     */
    public function getEventStreamId(): string
    {
        return $this->eventStreamId;
    }

    /**
     * @return boolean
     */
    public function isResolveLinkTos(): bool
    {
        return $this->resolveLinkTos;
    }

    /**
     * @return int
     */
    public function getStartFrom(): int
    {
        return $this->startFrom;
    }

    /**
     * @return int
     */
    public function getMessageTimeoutMilliseconds(): int
    {
        return $this->messageTimeoutMilliseconds;
    }

    /**
     * @return boolean
     */
    public function isRecordStatistics(): bool
    {
        return $this->recordStatistics;
    }

    /**
     * @return int
     */
    public function getLiveBufferSize(): int
    {
        return $this->liveBufferSize;
    }

    /**
     * @return int
     */
    public function getReadBatchSize(): int
    {
        return $this->readBatchSize;
    }

    /**
     * @return int
     */
    public function getBufferSize(): int
    {
        return $this->bufferSize;
    }

    /**
     * @return int
     */
    public function getMaxRetryCount(): int
    {
        return $this->maxRetryCount;
    }

    /**
     * @return boolean
     */
    public function isPreferRoundRobin(): bool
    {
        return $this->preferRoundRobin;
    }

    /**
     * @return int
     */
    public function getCheckPointAfterTime(): int
    {
        return $this->checkPointAfterTime;
    }

    /**
     * @return int
     */
    public function getCheckPointMaxCount(): int
    {
        return $this->checkPointMaxCount;
    }

    /**
     * @return int
     */
    public function getCheckPointMinCount(): int
    {
        return $this->checkPointMinCount;
    }

    /**
     * @return int
     */
    public function getSubscriberMaxCount(): int
    {
        return $this->subscriberMaxCount;
    }

    /**
     * @return string
     */
    public function getNamedConsumerStrategy(): string
    {
        return $this->namedConsumerStrategy;
    }
}
