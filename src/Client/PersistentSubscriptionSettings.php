<?php

namespace Mhwk\Ouro\Client;

final class PersistentSubscriptionSettings
{
    /**
     * @var bool
     */
    private $resolveLinkTos;

    /**
     * @var int
     */
    private $startFrom;

    /**
     * @var bool
     */
    private $extraStatistics;

    /**
     * @var TimeSpan
     */
    private $messageTimeout;

    /**
     * @var int
     */
    private $maxRetryCount;

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
    private $historyBufferSize;

    /**
     * @var TimeSpan
     */
    private $checkPointAfter;

    /**
     * @var int
     */
    private $minCheckPointCount;

    /**
     * @var int
     */
    private $maxCheckPointCount;

    /**
     * @var int
     */
    private $maxSubscriberCount;

    /**
     * @var string
     */
    private $namedConsumerStrategy;

    /**
     * @param bool $resolveLinkTos
     * @param int $startFrom
     * @param bool $extraStatistics
     * @param TimeSpan $messageTimeout
     * @param int $maxRetryCount
     * @param int $liveBufferSize
     * @param int $readBatchSize
     * @param int $historyBufferSize
     * @param TimeSpan $checkPointAfter
     * @param int $minCheckPointCount
     * @param int $maxCheckPointCount
     * @param int $maxSubscriberCount
     * @param string $namedConsumerStrategy
     */
    private function __construct(
        bool $resolveLinkTos,
        int $startFrom,
        bool $extraStatistics,
        TimeSpan $messageTimeout,
        int $maxRetryCount,
        int $liveBufferSize,
        int $readBatchSize,
        int $historyBufferSize,
        TimeSpan $checkPointAfter,
        int $minCheckPointCount,
        int $maxCheckPointCount,
        int $maxSubscriberCount,
        string $namedConsumerStrategy)
    {
        $this->resolveLinkTos = $resolveLinkTos;
        $this->startFrom = $startFrom;
        $this->extraStatistics = $extraStatistics;
        $this->messageTimeout = $messageTimeout;
        $this->maxRetryCount = $maxRetryCount;
        $this->liveBufferSize = $liveBufferSize;
        $this->readBatchSize = $readBatchSize;
        $this->historyBufferSize = $historyBufferSize;
        $this->checkPointAfter = $checkPointAfter;
        $this->minCheckPointCount = $minCheckPointCount;
        $this->maxCheckPointCount = $maxCheckPointCount;
        $this->maxSubscriberCount = $maxSubscriberCount;
        $this->namedConsumerStrategy = $namedConsumerStrategy;
    }

    public static function create()
    {
        return new self(
            true,
            0,
            false,
            TimeSpan::fromSeconds(30),
            500,
            500,
            10,
            20,
            TimeSpan::fromSeconds(2),
            10,
            1000,
            0,
            SystemConsumerStrategies::ROUND_ROBIN
        );
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
     * @return boolean
     */
    public function isExtraStatistics(): bool
    {
        return $this->extraStatistics;
    }

    /**
     * @return TimeSpan
     */
    public function getMessageTimeout(): TimeSpan
    {
        return $this->messageTimeout;
    }

    /**
     * @return int
     */
    public function getMaxRetryCount(): int
    {
        return $this->maxRetryCount;
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
    public function getHistoryBufferSize(): int
    {
        return $this->historyBufferSize;
    }

    /**
     * @return TimeSpan
     */
    public function getCheckPointAfter(): TimeSpan
    {
        return $this->checkPointAfter;
    }

    /**
     * @return int
     */
    public function getMinCheckPointCount(): int
    {
        return $this->minCheckPointCount;
    }

    /**
     * @return int
     */
    public function getMaxCheckPointCount(): int
    {
        return $this->maxCheckPointCount;
    }

    /**
     * @return int
     */
    public function getMaxSubscriberCount(): int
    {
        return $this->maxSubscriberCount;
    }

    /**
     * @return string
     */
    public function getNamedConsumerStrategy(): string
    {
        return $this->namedConsumerStrategy;
    }
}
