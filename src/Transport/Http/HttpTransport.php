<?php

namespace Mhwk\Ouro\Transport\Http;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use Mhwk\Ouro\Transport\Http\Handler\CreatePersistentSubscriptionHandler;
use Mhwk\Ouro\Transport\Http\Handler\DeletePersistentSubscriptionHandler;
use Mhwk\Ouro\Transport\Http\Handler\UpdatePersistentSubscriptionHandler;
use Mhwk\Ouro\Transport\Message\CreatePersistentSubscription;
use Mhwk\Ouro\Transport\Message\DeletePersistentSubscription;
use Mhwk\Ouro\Transport\Message\PersistentSubscriptionAckEvents;
use Mhwk\Ouro\Transport\Message\PersistentSubscriptionNakEvents;
use Mhwk\Ouro\Transport\Http\Handler\ConnectToPersistentSubscriptionHandler;
use Mhwk\Ouro\Transport\Message\ConnectToPersistentSubscription;
use Mhwk\Ouro\Transport\Message\ReadStreamEventsForward;
use Mhwk\Ouro\Transport\Message\UpdatePersistentSubscription;
use Mhwk\Ouro\Transport\Message\WriteEvents;
use Mhwk\Ouro\Exception\RuntimeException;
use Mhwk\Ouro\Transport\Http\Handler\PersistentSubscriptionAckEventsHandler;
use Mhwk\Ouro\Transport\Http\Handler\PersistentSubscriptionNakEventsHandler;
use Mhwk\Ouro\Transport\Http\Handler\ReadStreamEventsForwardHandler;
use Mhwk\Ouro\Transport\Http\Handler\WriteEventsHandler;
use Mhwk\Ouro\Transport\IHandleMessage;
use Mhwk\Ouro\Transport\Message\UserCredentials;

final class HttpTransport implements IHandleMessage
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var UserCredentials
     */
    private $credentials;

    /**
     * @var IHandleMessage
     */
    private $handlers;

    /**
     * @param ClientInterface $client
     * @param UserCredentials $credentials
     */
    function __construct(ClientInterface $client, UserCredentials $credentials)
    {
        $this->client = $client;
        $this->credentials = $credentials;
        $this->handlers[ReadStreamEventsForward::class] = new ReadStreamEventsForwardHandler($this->client, $this->credentials);
        $this->handlers[WriteEvents::class] = new WriteEventsHandler($this->client, $this->credentials);
        $this->handlers[CreatePersistentSubscription::class] = new CreatePersistentSubscriptionHandler($this->client, $this->credentials);
        $this->handlers[UpdatePersistentSubscription::class] = new UpdatePersistentSubscriptionHandler($this->client, $this->credentials);
        $this->handlers[DeletePersistentSubscription::class] = new DeletePersistentSubscriptionHandler($this->client, $this->credentials);
        $this->handlers[ConnectToPersistentSubscription::class] = new ConnectToPersistentSubscriptionHandler($this->client, $this->credentials);
        $this->handlers[PersistentSubscriptionAckEvents::class] = new PersistentSubscriptionAckEventsHandler($this->client, $this->credentials);
        $this->handlers[PersistentSubscriptionNakEvents::class] = new PersistentSubscriptionNakEventsHandler($this->client, $this->credentials);
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
            new GuzzleClient([
                'base_uri' => rtrim($baseUrl, '/'),
                'timeout' => 2.0
            ]),
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
