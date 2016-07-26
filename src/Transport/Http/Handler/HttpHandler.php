<?php

namespace Mhwk\Ouro\Transport\Http\Handler;

use Assert\Assertion;
use Generator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Icicle\Coroutine\Coroutine;
use Icicle\Loop;
use Mhwk\Ouro\Transport\IHandleMessage;
use Mhwk\Ouro\Transport\Message\UserCredentials;
use Throwable;

abstract class HttpHandler implements IHandleMessage
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var UserCredentials
     */
    private $credentials;

    /**
     * @param Client $client
     * @param UserCredentials $credentials
     */
    public function __construct(Client $client, UserCredentials $credentials)
    {
        $this->client = $client;
        $this->credentials = $credentials;
    }

    /**
     * @param object $message
     *
     * @return Response
     */
    public function handle($message)
    {
        $this->assert($message);
        return $this->request($message);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    protected function send(Request $request): Generator
    {
        try {
            $response = yield $this->client->send($request, [
                'auth' => [$this->credentials->getUsername(), $this->credentials->getPassword()]
            ]);
            return $response;
        } catch (RequestException $error) {
            $this->assertResponse($error->getResponse());
            return $error->getResponse();
        }
    }

    /**
     * @param Response $response
     */
    protected function assertResponse(Response $response)
    {
        Assertion::inArray(
            $response->getStatusCode(),
            [200, 201, 202],
            sprintf('Failed request [%s]: %s', $response->getStatusCode(), $response->getReasonPhrase())
        );
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
