<?php

namespace Spray\Ouro\Transport\Http;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use Spray\Ouro\Transport\Http\Handler\CreatePersistentSubscriptionHandler;
use Spray\Ouro\Transport\Http\Handler\DeletePersistentSubscriptionHandler;
use Spray\Ouro\Transport\Http\Handler\UpdatePersistentSubscriptionHandler;
use Spray\Ouro\Transport\Message\CreatePersistentSubscription;
use Spray\Ouro\Transport\Message\DeletePersistentSubscription;
use Spray\Ouro\Transport\Message\PersistentSubscriptionAckEvents;
use Spray\Ouro\Transport\Message\PersistentSubscriptionNakEvents;
use Spray\Ouro\Transport\Http\Handler\ConnectToPersistentSubscriptionHandler;
use Spray\Ouro\Transport\Message\ConnectToPersistentSubscription;
use Spray\Ouro\Transport\Message\ReadStreamEventsForward;
use Spray\Ouro\Transport\Message\UpdatePersistentSubscription;
use Spray\Ouro\Transport\Message\WriteEvents;
use Spray\Ouro\Exception\RuntimeException;
use Spray\Ouro\Transport\Http\Handler\PersistentSubscriptionAckEventsHandler;
use Spray\Ouro\Transport\Http\Handler\PersistentSubscriptionNakEventsHandler;
use Spray\Ouro\Transport\Http\Handler\ReadStreamEventsForwardHandler;
use Spray\Ouro\Transport\Http\Handler\WriteEventsHandler;
use Spray\Ouro\Transport\IHandleMessage;
use Spray\Ouro\Transport\Message\UserCredentials;

final class HttpTransport implements IHandleMessage
{
    /**
     * @var HttpConfiguration
     */
    private $configuration;

    /**
     * @var UserCredentials
     */
    private $credentials;

    /**
     * @var IHandleMessage[]
     */
    private $handlers;

    /**
     * @param HttpConfiguration $configuration
     * @param UserCredentials $credentials
     */
    function __construct(HttpConfiguration $configuration, UserCredentials $credentials)
    {
        $this->configuration = $configuration;
        $this->credentials = $credentials;
        $this->handlers[ReadStreamEventsForward::class] = new ReadStreamEventsForwardHandler($this->configuration, $this->credentials);
        $this->handlers[WriteEvents::class] = new WriteEventsHandler($this->configuration, $this->credentials);
        $this->handlers[CreatePersistentSubscription::class] = new CreatePersistentSubscriptionHandler($this->configuration, $this->credentials);
        $this->handlers[UpdatePersistentSubscription::class] = new UpdatePersistentSubscriptionHandler($this->configuration, $this->credentials);
        $this->handlers[DeletePersistentSubscription::class] = new DeletePersistentSubscriptionHandler($this->configuration, $this->credentials);
        $this->handlers[ConnectToPersistentSubscription::class] = new ConnectToPersistentSubscriptionHandler($this->configuration, $this->credentials);
        $this->handlers[PersistentSubscriptionAckEvents::class] = new PersistentSubscriptionAckEventsHandler($this->configuration, $this->credentials);
        $this->handlers[PersistentSubscriptionNakEvents::class] = new PersistentSubscriptionNakEventsHandler($this->configuration, $this->credentials);
    }

    /**
     * @param string $baseUrl
     * @param string $username
     * @param string $password
     *
     * @return HttpTransport
     */
    static function factory($baseUrl, $username, $password)
    {
        return new HttpTransport(
            new HttpConfiguration($baseUrl),
            new UserCredentials($username, $password)
        );
    }

    /**
     * @param object $message
     */
    function handle($message)
    {
        if ( ! isset($this->handlers[get_class($message)])) {
            throw new RuntimeException(sprintf(
                'No handler registered for message %s',
                get_class($message)
            ));
        }

        return $this->handlers[get_class($message)]->handle($message);
    }
}
