<?php

namespace Mhwk\Ouro\Client;

use Generator;
use Mhwk\Ouro\Message\ConnectToPersistentSubscription;
use Mhwk\Ouro\Message\PersistentSubscriptionAckEvents;
use Mhwk\Ouro\Message\PersistentSubscriptionNakEvents;
use Mhwk\Ouro\Transport\IHandleMessage;
use React\EventLoop\LoopInterface;

final class PersistentSubscription implements IConnectToPersistentSubscription, IConfirmEvent
{
    /**
     * @var IHandleMessage
     */
    private $transport;

    /**
     * @param LoopInterface $loop
     * @param IHandleMessage $transport
     */
    public function __construct(IHandleMessage $transport)
    {
        $this->transport = $transport;
    }

    /**
     * Connect to a persistent subscription.
     *
     * @param string $subscriptionId
     * @param string $streamId
     * @param int $allowedInFlightMessages
     * @param callable $onEventAppeared
     *
     * @return Generator
     */
    public function connect(
        string $subscriptionId,
        string $streamId,
        int $allowedInFlightMessages,
        callable $onEventAppeared): Generator
    {
        $observable = $this->transport->handle(new ConnectToPersistentSubscription($subscriptionId, $streamId, $allowedInFlightMessages));
        $iterator = $observable->getIterator();

        while (yield $iterator->isValid()) {
            yield from $onEventAppeared($iterator->getCurrent());
        }
    }

    /**
     * @param string $subscriptionId
     * @param string $streamId
     * @param array $processedStreamIds
     *
     * @return Generator
     */
    public function acknowledge(string $subscriptionId, string $streamId, array $processedStreamIds): Generator
    {
        yield from $this->transport->handle(new PersistentSubscriptionAckEvents($subscriptionId, $streamId, $processedStreamIds));
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
    public function fail(string $subscriptionId, string $streamId, array $processedStreamIds, string $message, int $action): Generator
    {
        yield from $this->transport->handle(new PersistentSubscriptionNakEvents($subscriptionId, $streamId, $processedStreamIds, $message, $action));
    }
}
