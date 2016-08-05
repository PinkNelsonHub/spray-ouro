<?php

namespace Spray\Ouro\Transport\Http\Handler;

use Assert\Assertion;
use Generator;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Throwable;
use Spray\Ouro\Transport\IHandleMessage;
use Spray\Ouro\Transport\Message\UserCredentials;

abstract class HttpHandler implements IHandleMessage
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
     * @param ClientInterface $client
     * @param UserCredentials $credentials
     */
    public function __construct(ClientInterface $client, UserCredentials $credentials)
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
        $response = yield $this->client->send($request, [
            'auth' => [$this->credentials->getUsername(), $this->credentials->getPassword()]
        ]);
        return $response;
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
