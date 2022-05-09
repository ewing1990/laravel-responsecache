<?php

namespace Spatie\ResponseCache;

use Illuminate\Cache\Repository;
use Spatie\ResponseCache\Serializers\Serializer;
use Symfony\Component\HttpFoundation\Response;

class ResponseCacheRepository
{
    protected Repository $cache;

    protected Serializer $responseSerializer;

    public function __construct(Serializer $responseSerializer, Repository $cache)
    {
        $this->cache = $cache;

        $this->responseSerializer = $responseSerializer;
    }

    /**
     * @param string $key
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param \DateTime|int $seconds
     */
    public function put(string $key, $response, $seconds)
    {
        $this->cache->put($key, $this->responseSerializer->serialize($response), is_numeric($seconds) ? now()->addSeconds($seconds) : $seconds);
    }

    public function has(string $key): bool
    {
        return $this->cache->has($key);
    }

    public function get(string $key): ?Response
    {
        $response = $this->cache->get($key);
        
        return $response ? $this->responseSerializer->unserialize($response) : $response;
    }

    public function clear()
    {
        $this->cache->clear();
    }

    public function forget(string $key): bool
    {
        return $this->cache->forget($key);
    }

    public function tags(array $tags): self
    {
        return new self($this->responseSerializer, $this->cache->tags($tags));
    }
}
