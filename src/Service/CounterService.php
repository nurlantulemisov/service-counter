<?php

declare(strict_types=1);

namespace Nurlantulemisov\ServiceCounter\Service;

use DomainException;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class CounterService
{
    private CacheItemPoolInterface $cache;

    private ContainerInterface $container;

    public function __construct(ContainerInterface $container, CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
        $this->container = $container;
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

    public function updateCount(string $locale): void
    {
        try {
            $updater = $this->container->get(CountUpdater::class);
            $updater($locale);
        } catch (NotFoundExceptionInterface | ContainerExceptionInterface | DomainException $e) {
            print_r($e->getMessage());
            exit;
        }
    }
}
