<?php

declare(strict_types=1);

namespace Nurlantulemisov\ServiceCounter\Service;

use DomainException;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

class CountUpdater
{
    private CacheItemPoolInterface $cache;

    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @throws DomainException
     */
    public function __invoke(string $locale): void
    {
        try {
            $cacheItem = $this->cache->getItem($locale);
        } catch (InvalidArgumentException $e) {
            throw new DomainException('Нельзя обновить данные');
        }

        $count = 0;
        if ($cacheItem->isHit()) {
            $count = (int)$cacheItem->get();
        }

        $count++;
        $cacheItem = $cacheItem->set($count);
        $this->cache->save($cacheItem);
    }
}
