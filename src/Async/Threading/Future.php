<?php

namespace Mhwk\Ouro\Async\Threading;

use Mhwk\Ouro\Async\IFuture;
use Thread;

class Future extends Thread implements IFuture, ICollectable
{
    /**
     * @var callable
     */
    private $callable;

    /**
     * @var array
     */
    private $args;

    /**
     * @var bool
     */
    private $awake = true;

    /**
     * @var mixed
     */
    private $result;

    /**
     * @var bool
     */
    private $garbage = false;

    /**
     * @param callable $callable
     * @param array $args
     */
    private function __construct(callable $callable, array $args = [])
    {
        $this->callable = $callable;
        $this->args = $args;
    }

    /**
     * @param $closure
     * @param $args
     *
     * @return Future
     */
    public static function for($closure, array $args = [])
    {
        $future = new self($closure, $args);
        ThreadPool::queueThread($future);
        return $future;
    }

    /**
     * Runs the closure synchronized and stores the result in $this->result.
     *
     * @return void
     */
    public function run()
    {
        $this->synchronized(function () {
            var_dump('before');
            $this->result = ($this->callable)(...$this->args);
            $this->awake = false;
            var_dump('after');
            $this->notify();
            var_dump('notified');
        });
    }

    /**
     * @return mixed
     */
    public function result()
    {
        return $this->synchronized(function() {
            while ($this->awake) {
                $this->wait();
            }
            return $this->result;
        });
    }

    /**
     * @return bool
     */
    function isGarbage(): bool
    {
        return $this->garbage;
    }
}
