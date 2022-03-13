<?php

declare(strict_types=1);

namespace Nurlantulemisov\ServiceCounter\Service;

use DomainException;
use Nurlantulemisov\ServiceCounter\ReadModel\CountryStat;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Intl\Languages;

class CountSupplier
{
    private CacheItemPoolInterface $cache;

    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @return CountryStat[]
     *
     * @throws DomainException
     */
    public function __invoke(): array
    {
        $languages = array_keys(Languages::getNames());
        $keys = array_map(static fn(string $locale): string => $locale, $languages);

        try {
            $items = $this->cache->getItems($keys);
        } catch (InvalidArgumentException $e) {
            throw new DomainException('Нельзя получить данные');
        }

        $stats = [];
        foreach ($items as $item) {
            /**
             * @var CacheItemInterface $item
             */
            if ($item->isHit()) {
                $stats[] = new CountryStat($item->getKey(), (int)$item->get());
            }
        }

        return $stats;
    }
}
