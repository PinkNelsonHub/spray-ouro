<?php

use Icicle\Awaitable;
use Icicle\Concurrent\Worker\DefaultPool;
use Icicle\Concurrent\Worker\Environment;
use Icicle\Concurrent\Worker\Task;
use Icicle\Coroutine;
use Icicle\Loop;

error_reporting(-1);
ini_set('display_errors', 1);

chdir(__DIR__);
define('HHVM_VERSION', false);
require 'vendor/autoload.php';

class CallableTask implements Task
{
    /**
     * @var callable
     */
    private $callable;

    /**
     * @var array
     */
    private $args;

    public function __construct(callable $callable, array $args = [])
    {
        $this->callable = $callable;
        $this->args = $args;
    }

    public function run(Environment $environment)
    {
        ($this->callable)(...$this->args);
    }
}

function wait()
{
    $sleep = rand(1, 200) / 100;
    echo "Sleep {$sleep} seconds\n";
    sleep($sleep);
    echo "Awake\n";
    return true;
}

Coroutine\create(function() {
    $pool = new DefaultPool();
    $pool->start();

    $coroutines = [];

    for ($i = 0; $i < 50; $i++) {
        $coroutines[] = Coroutine\create(function() use ($pool) {
            $result = yield from $pool->enqueue(new CallableTask('wait'));
            return $result;
        });
    }

    yield Awaitable\all($coroutines);

    return yield from $pool->shutdown();
})->done();

Loop\run();
