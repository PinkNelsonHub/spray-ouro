<?php

namespace Mhwk\Ouro\Async\Threading;

use Mhwk\Ouro\Exception\RuntimeException;
use Pool;
use Thread;
use Threaded;
use Worker;

final class ThreadPool
{
    /**
     * @var ThreadPool
     */
    private static $instance;

    /**
     * @var Pool
     */
    private $pool;

    /**
     * @param int $size
     */
    private function __construct(int $size)
    {
        $this->pool = new Pool($size, Worker::class);
    }

    /**
     * @return ThreadPool
     */
    private static function instance()
    {
        if (null === self::$instance) {
            class_exists(Deferred::class);
            class_exists(Future::class);
            class_exists(Promise::class);
            class_exists(Task::class);
            self::$instance = new ThreadPool(Utility::cpus());
        }
        return self::$instance;
    }

    /**
     * @param Thread $thread
     *
     * @return void
     */
    static function queueThread(Thread $thread)
    {
        if ( ! $thread instanceof ICollectable) {
            throw new RuntimeException(sprintf(
                'Only collectable threads can be added to the pool'
            ));
        }
        self::instance()->pool->submit($thread);
    }

    /**
     * @param callable $callable
     *
     * @return void
     */
    static function queueCallable(callable $callable)
    {
        self::queueThread(new class ($callable) extends Thread implements ICollectable {
            /**
             * @var callable
             */
            private $callable;

            /**
             * @var bool
             */
            private $garbage = false;

            /**
             * @param callable $callable
             */
            public function __construct(callable $callable)
            {
                $this->callable = $callable;
            }

            /**
             * Run the closure.
             *
             * @return void
             */
            public function run()
            {
                ($this->callable)();
                $this->garbage = true;
            }

            /**
             * @return bool
             */
            function isGarbage()
            {
                return $this->garbage;
            }
        });
    }

    /**
     * Run threads until all are complete.
     *
     * @return void
     */
    public static function run()
    {
        self::instance()->pool->collect(function (Threaded $work) {
            return $work->isGarbage();
        });
        self::instance()->pool->shutdown();
    }
}
