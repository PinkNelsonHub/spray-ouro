<?php

namespace Spray\Ouro\Dns;

use Icicle\Dns\Resolver\Resolver;

final class CachedResolver implements Resolver
{
    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @var array
     */
    private $cache = [];

    /**
     * @param Resolver $resolver
     */
    public function __construct(Resolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @coroutine
     *
     * @param   string $domain Domain name to resolve.
     * @param   mixed[] $options
     *
     * @return  \Generator
     *
     * @resolve string[] List of IP address. May return an empty array if the host cannot be found.
     *
     * @throws \Icicle\Dns\Exception\FailureException If the server returns a non-zero response code.
     */
    public function resolve(string $domain, array $options = []): \Generator
    {
        if ( ! isset($this->cache[$domain])) {
            $this->cache[$domain] = iterator_to_array($this->resolver->resolve($domain, $options));
        }
        foreach ($this->cache[$domain] as $resolved) {
            yield $resolved;
        }
    }
}
