<?php

namespace Spray\Ouro\Transport\Http\Handler;

use Generator;
use Icicle\Dns;
use Icicle\Dns\Executor\BasicExecutor;
use Icicle\Dns\Executor\MultiExecutor;
use Icicle\Dns\Resolver\BasicResolver;
use Icicle\Http\Client\Client;
use Icicle\Http\Driver\Encoder\Http1Encoder;
use Icicle\Http\Exception\Throwable;
use Icicle\Http\Message\BasicRequest;
use Icicle\Http\Message\BasicUri;
use Icicle\Http\Message\Response;
use Icicle\Http\Stream\ChunkedEncoder;
use Spray\Ouro\Dns\CachedResolver;
use Spray\Ouro\Exception\EventStoreUnreachableException;
use Spray\Ouro\Transport\Http\HttpConfiguration;
use Spray\Ouro\Transport\Http\HttpRequest;
use Spray\Ouro\Transport\Http\HttpResponse;
use Spray\Ouro\Transport\IHandleMessage;
use Spray\Ouro\Transport\Message\UserCredentials;

abstract class HttpHandler implements IHandleMessage
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
     * @param HttpConfiguration $configuration
     * @param UserCredentials $credentials
     */
    public function __construct(HttpConfiguration $configuration, UserCredentials $credentials)
    {
        $this->configuration = $configuration;
        $this->credentials = $credentials;
    }

    /**
     * @param object $message
     *
     * @return mixed
     */
    public function handle($message)
    {
        $this->assert($message);
        return $this->request($message);
    }

    /**
     * @param HttpRequest $httpRequest
     *
     * @return HttpResponse
     */
    protected function send(HttpRequest $httpRequest): Generator
    {
        try {
            Dns\resolver(new BasicResolver(new BasicExecutor('127.0.0.1')));

            $client = new Client();

            /** @var Response $response */
            $response = yield from $client->send(new BasicRequest(
                $httpRequest->method(),
                (new BasicUri($this->configuration->getBaseUrl()))
                    ->withPath($httpRequest->path())
                    ->withQuery($httpRequest->query())
                    ->withUser(
                        $this->credentials->getUsername(),
                        $this->credentials->getPassword()
                    ),
                $httpRequest->headers(),
                new ChunkedEncoder(0, $httpRequest->body())
            ));

            $data = '';
            $stream = $response->getBody();
            while ($stream->isReadable()) {
                $data = yield from $stream->read();
            }

            switch ($response->getStatusCode()) {
                default:
                    throw new EventStoreUnreachableException(
                        sprintf(
                            'Invalid response from eventstore: [%s] %s',
                            $response->getStatusCode(),
                            $response->getReasonPhrase()
                        ),
                        $response->getStatusCode()
                    );
                case 200:
                    return new HttpResponse($response->getStatusCode(), $data);
                    break;
            }

        } catch (Throwable $error) {
            throw new EventStoreUnreachableException(
                sprintf(
                    'An exception occurred while communicating with eventstore: %s',
                    $error->getMessage()
                ),
                $error->getCode(),
                $error
            );
        }
    }

    /**
     * Assert that the command can be handled.
     *
     * @param object $command
     *
     * @return void
     */
    abstract function assert($command);

    /**
     * Handle the command.
     *
     * @param object $command
     *
     * @return object
     */
    abstract function request($command);
}
