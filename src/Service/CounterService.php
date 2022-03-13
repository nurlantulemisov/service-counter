<?php

declare(strict_types=1);

namespace Nurlantulemisov\ServiceCounter\Service;

use Psr\Cache\CacheItemPoolInterface;

class CounterService
{
    private CacheItemPoolInterface $cache;

    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }

    public function getCount(string $userHash): int
    {
        $cacheItem = $this->cache->getItem('count-user-' . $userHash);
        $count = 0;
        if ($cacheItem->isHit()) {
            $count = (int)$cacheItem->get();
        }
        $count++;
        $cacheItem = $cacheItem->set($count);
        $this->cache->save($cacheItem);
        return $count;
    }
}
