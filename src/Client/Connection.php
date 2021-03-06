<?php

namespace Spray\Ouro\Client;

use Generator;
use Icicle\Observable\Observable;
use Illuminate\Support\Facades\Log;
use Spray\Ouro\Exception\ConcurrencyException;
use Spray\Ouro\Exception\EventNotExecutedException;
use Spray\Ouro\Exception\EventNotSupportedException;
use Spray\Ouro\Exception\Exception;
use Spray\Ouro\Transport\Message\ConnectToPersistentSubscription;
use Spray\Ouro\Transport\Message\CreatePersistentSubscription;
use Spray\Ouro\Transport\Message\DeletePersistentSubscription;
use Spray\Ouro\Transport\Message\NakAction;
use Spray\Ouro\Transport\Message\NewEvent;
use Spray\Ouro\Transport\Message\PersistentSubscriptionAckEvents;
use Spray\Ouro\Transport\Message\PersistentSubscriptionNakEvents;
use Spray\Ouro\Transport\Message\ReadStreamEventsComplete;
use Spray\Ouro\Transport\Message\ReadStreamEventsForward;
use Spray\Ouro\Transport\Message\ResolvedIndexedEvent;
use Spray\Ouro\Transport\Message\UpdatePersistentSubscription;
use Spray\Ouro\Transport\Message\WriteEvents;
use Spray\Ouro\Transport\Http\HttpTransport;
use Spray\Ouro\Transport\IHandleMessage;
use Throwable;
use Icicle\Coroutine;

final class Connection
    implements IConnectToEventStore,
               IConnectedToEventStore
{
    /**
     * @var IHandleMessage
     */
    private $transport;

    /**
     * @param IHandleMessage $transport
     */
    private function __construct(
        IHandleMessage $transport)
    {
        $this->transport = $transport;
    }

    /**
     * @param string $host
     * @param string $username
     * @param string $password
     *
     * @return IConnectedToEventStore
     */
    static function connect(string $host, string $username, string $password): IConnectedToEventStore
    {
        return new Connection(HttpTransport::factory($host, $username, $password));
    }

    /**
     * @param string $stream
     * @param int $expectedEventNumber
     * @param NewEvent[] $events
     *
     * @return Coroutine\Coroutine
     */
    function writeEventsAsync(string $stream, int $expectedEventNumber, array $events): Coroutine\Coroutine
    {
        return Coroutine\create(
            [$this, 'writeEvents'],
            $stream,
            $expectedEventNumber,
            $events
        );
    }

    /**
     * @param string $stream
     * @param int $expectedEventNumber
     * @param array $events
     *
     * @return Generator
     */
    function writeEvents(string $stream, int $expectedEventNumber, array $events): Generator
    {
        $result = yield $this->transport->handle(new WriteEvents($stream, $expectedEventNumber, $events, false));

        return $result;
    }

    /**
     * @param string $stream
     * @param callable $onEventAppeared
     *
     * @return Coroutine\Coroutine
     */
    function readStreamEventsForwardAsync(string $stream, callable $onEventAppeared, int $start = 0, int $limit = null): Coroutine\Coroutine
    {
        return Coroutine\create(
            [$this, 'readStreamEventsForward'],
            $stream,
            $onEventAppeared,
            $start,
            $limit
        );
    }

    /**
     * @param string $stream
     * @param callable $onEventAppeared
     * @param int $start
     * @param int $limit
     *
     * @return Generator
     */
    function readStreamEventsForward(
        string $stream,
        callable $onEventAppeared,
        int $start = 0,
        int $limit = null): Generator
    {
        $head = false;
        $count = 50;

        while (!$head) {
            /** @var ReadStreamEventsComplete $readStreamEventsComplete */
            /** @var ResolvedIndexedEvent $resolvedIndexEvent */
            if (null !== $limit && $start + $count >= $limit) {
                $count = $limit - $start;
            }

            $readStreamEventsComplete = yield $this->transport->handle(new ReadStreamEventsForward(
                $stream,
                $start,
                $count,
                false
            ));

            foreach ($readStreamEventsComplete->getEvents() as $resolvedIndexEvent) {
                $result = $onEventAppeared($resolvedIndexEvent->getEvent());
                if ($result instanceof Generator) {
                    yield from $result;
                } else {
                    yield;
                }
            }

            $head = $readStreamEventsComplete->isEndOfStream();
            $start += $count;

            if (null !== $limit && $start + $count >= $limit) {
                break;
            }
        }
    }

    /**
     * @param string $groupName
     * @param string $streamId
     * @param PersistentSubscriptionSettings $settings
     *
     * @return Coroutine\Coroutine
     */
    function createPersistentSubscriptionAsync(
        string $groupName,
        string $streamId,
        PersistentSubscriptionSettings $settings = null): Coroutine\Coroutine
    {
        return Coroutine\create(
            [$this, 'createPersistentSubscription'],
            $groupName,
            $streamId,
            $settings
        );
    }

    /**
     * @param string $groupName
     * @param string $streamId
     * @param PersistentSubscriptionSettings $settings
     *
     * @return Generator
     */
    function createPersistentSubscription(
        string $groupName,
        string $streamId,
        PersistentSubscriptionSettings $settings = null): Generator
    {
        $settings = $settings ?: PersistentSubscriptionSettings::create();
        $result = yield $this->transport->handle(new CreatePersistentSubscription(
            $groupName,
            $streamId,
            $settings->isResolveLinkTos(),
            $settings->getStartFrom(),
            $settings->getMessageTimeout()->toMilliseconds(),
            $settings->isExtraStatistics(),
            $settings->getLiveBufferSize(),
            $settings->getReadBatchSize(),
            $settings->getHistoryBufferSize(),
            $settings->getMaxRetryCount(),
            $settings->getNamedConsumerStrategy() === SystemConsumerStrategies::ROUND_ROBIN,
            $settings->getCheckPointAfter()->toMilliseconds(),
            $settings->getMaxCheckPointCount(),
            $settings->getMinCheckPointCount(),
            $settings->getMaxSubscriberCount(),
            $settings->getNamedConsumerStrategy()
        ));

        return $result;
    }

    /**
     * @param string $groupName
     * @param string $streamId
     * @param PersistentSubscriptionSettings $settings
     *
     * @return Coroutine\Coroutine
     */
    function updatePersistentSubscriptionAsync(
        string $groupName,
        string $streamId,
        PersistentSubscriptionSettings $settings = null): Coroutine\Coroutine
    {
        return Coroutine\create(
            [$this, 'updatePersistentSubscription'],
            $groupName,
            $streamId,
            $settings
        );
    }

    /**
     * @param string $groupName
     * @param string $streamId
     * @param PersistentSubscriptionSettings $settings
     *
     * @return Generator
     */
    function updatePersistentSubscription(
        string $groupName,
        string $streamId,
        PersistentSubscriptionSettings $settings = null): Generator
    {
        $settings = $settings ?: PersistentSubscriptionSettings::create();
        $result = yield $this->transport->handle(new UpdatePersistentSubscription(
            $groupName,
            $streamId,
            $settings->isResolveLinkTos(),
            $settings->getStartFrom(),
            $settings->getMessageTimeout()->toMilliseconds(),
            $settings->isExtraStatistics(),
            $settings->getLiveBufferSize(),
            $settings->getReadBatchSize(),
            $settings->getHistoryBufferSize(),
            $settings->getMaxRetryCount(),
            $settings->getNamedConsumerStrategy() === SystemConsumerStrategies::ROUND_ROBIN,
            $settings->getCheckPointAfter()->toMilliseconds(),
            $settings->getMaxCheckPointCount(),
            $settings->getMinCheckPointCount(),
            $settings->getMaxSubscriberCount(),
            $settings->getNamedConsumerStrategy()
        ));

        return $result;
    }

    /**
     * @param string $groupName
     * @param string $streamId
     *
     * @return Coroutine\Coroutine
     */
    function deletePersistentSubscriptionAsync(
        string $groupName,
        string $streamId): Coroutine\Coroutine
    {
        return Coroutine\create(
            [$this, 'deletePersistentSubscription'],
            $groupName,
            $streamId
        );
    }

    /**
     * @param string $groupName
     * @param string $streamId
     *
     * @return Generator
     */
    function deletePersistentSubscription(
        string $groupName,
        string $streamId): Generator
    {
        $result = yield $this->transport->handle(new DeletePersistentSubscription(
            $groupName,
            $streamId
        ));

        return $result;
    }

    /**
     * Connect to a persistent subscription.
     *
     * @param string $subscriptionId
     * @param string $streamId
     * @param int $allowedInFlightMessages
     * @param callable $onEventAppeared
     *
     * @return Coroutine\Coroutine
     */
    function subscribePersistentAsync(
        string $subscriptionId,
        string $streamId,
        int $allowedInFlightMessages,
        callable $onEventAppeared,
        callable $onSubscriptionDropped,
        bool $autoAck = true): Coroutine\Coroutine
    {
        return Coroutine\create(
            [$this, 'subscribePersistent'],
            $subscriptionId,
            $streamId,
            $allowedInFlightMessages,
            $onEventAppeared,
            $onSubscriptionDropped,
            $autoAck
        );
    }

    /**
     * @param string $subscriptionId
     * @param string $streamId
     * @param int $allowedInFlightMessages
     * @param callable $onEventAppeared
     *
     * @return Generator
     */
    function subscribePersistent(
        string $subscriptionId,
        string $streamId,
        int $allowedInFlightMessages,
        callable $onEventAppeared,
        callable $onSubscriptionDropped,
        bool $autoAck = true): Generator
    {
        while (true) {
            try {
                /** @var Observable $observable */
                $observable = $this->transport->handle(new ConnectToPersistentSubscription(
                    $subscriptionId,
                    $streamId,
                    $allowedInFlightMessages
                ));
                break;
            } catch (Exception $error) {
                yield $onSubscriptionDropped($error);
            }
        }

        $iterator = $observable->getIterator();
        while (yield $iterator->isValid()) {
            try {
                yield $onEventAppeared($iterator->getCurrent()->getEvent()->getEvent());

                if ($autoAck) {
                    yield $this->acknowledgeAsync(
                        $subscriptionId,
                        $streamId,
                        [$iterator->getCurrent()->getEvent()->getEvent()->getEventId()]
                    );
                }
            } catch (EventNotSupportedException $error) {
                yield $this->failAsync(
                    $subscriptionId,
                    $streamId,
                    [$iterator->getCurrent()->getEvent()->getEvent()->getEventId()],
                    $error->getMessage(),
                    NakAction::SKIP
                );
            } catch (ConcurrencyException $error) {
                yield $this->failAsync(
                    $subscriptionId,
                    $streamId,
                    [$iterator->getCurrent()->getEvent()->getEvent()->getEventId()],
                    $error->getMessage(),
                    NakAction::RETRY
                );
            } catch (EventNotExecutedException $error) {
                yield $this->failAsync(
                    $subscriptionId,
                    $streamId,
                    [$iterator->getCurrent()->getEvent()->getEvent()->getEventId()],
                    $error->getMessage(),
                    NakAction::PARK
                );
            } catch (Throwable $error) {
                yield $this->failAsync(
                    $subscriptionId,
                    $streamId,
                    [$iterator->getCurrent()->getEvent()->getEvent()->getEventId()],
                    $error->getMessage(),
                    NakAction::STOP
                );
                throw $error;
            }
        }
    }

    /**
     * @param string $subscriptionId
     * @param string $streamId
     * @param array $processedStreamIds
     *
     * @return Coroutine\Coroutine
     */
    function acknowledgeAsync(string $subscriptionId, string $streamId, array $processedStreamIds): Coroutine\Coroutine
    {
        return Coroutine\create(
            [$this, 'acknowledge'],
            $subscriptionId,
            $streamId,
            $processedStreamIds
        );
    }

    /**
     * @param string $subscriptionId
     * @param string $streamId
     * @param array $processedStreamIds
     *
     * @return Generator
     */
    function acknowledge(string $subscriptionId, string $streamId, array $processedStreamIds): Generator
    {
        $result = yield from $this->transport->handle(new PersistentSubscriptionAckEvents(
            $subscriptionId,
            $streamId,
            $processedStreamIds
        ));

        return $result;
    }

    /**
     * @param string $subscriptionId
     * @param string $streamId
     * @param array $processedStreamIds
     * @param string $message
     * @param int $action
     *
     * @return Coroutine\Coroutine
     */
    function failAsync(
        string $subscriptionId,
        string $streamId,
        array $processedStreamIds,
        string $message,
        int $action): Coroutine\Coroutine
    {
        return Coroutine\create(
            [$this, 'fail'],
            $subscriptionId,
            $streamId,
            $processedStreamIds,
            $message,
            $action
        );
    }

    /**
     * @param string $subscriptionId
     * @param string $streamId
     * @param array $processedStreamIds
     * @param string $message
     * @param int $action
     *
     * @return Generator
     */
    function fail(
        string $subscriptionId,
        string $streamId,
        array $processedStreamIds,
        string $message,
        int $action): Generator
    {
        $result = yield from $this->transport->handle(new PersistentSubscriptionNakEvents(
            $subscriptionId,
            $streamId,
            $processedStreamIds,
            $message,
            new NakAction($action)
        ));

        return $result;
    }
}
