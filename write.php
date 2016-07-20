<?php

use Mhwk\Ouro\Client\Connection;
use Mhwk\Ouro\Transport\Message\NewEvent;
use Icicle\Awaitable;
use Icicle\Coroutine;
use Icicle\Loop;
use Ramsey\Uuid\Uuid;

error_reporting(-1);
ini_set('display_errors', 1);

chdir(__DIR__);
require 'vendor/autoload.php';

Coroutine\create(function() {
    $connection = Connection::connect('eventstore:2113', 'admin', 'changeit');
    for ($i = 0; $i < 100; $i++) {
        yield $connection->writeEvents('bar', -2, [
            new NewEvent(
                Uuid::uuid4(),
                'Bar',
                ['foo' => 'bar'],
                ['foo' => 'bar']
            ),
            new NewEvent(
                Uuid::uuid4(),
                'Bar',
                ['foo' => 'bar'],
                ['foo' => 'bar']
            )
        ]);
    }
});

Loop\run();
