<?php

declare(strict_types=1);

namespace Nurlantulemisov\ServiceCounter\Service;

use DomainException;
use Nurlantulemisov\ServiceCounter\ReadModel\CountryStat;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class CounterService
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @throws DomainException
     */
    public function updateCount(string $locale): void
    {
        try {
            $updater = $this->container->get(CountUpdater::class);
            $updater($locale);
        } catch (NotFoundExceptionInterface | ContainerExceptionInterface | DomainException $e) {
            throw new DomainException($e->getMessage());
        }
    }

    /**
     * @return CountryStat[]
     *
     * @throws DomainException
     */
    public function getStats(): array
    {
        try {
            $supplier = $this->container->get(CountSupplier::class);
            return $supplier();
        } catch (NotFoundExceptionInterface | ContainerExceptionInterface | DomainException $e) {
            throw new DomainException($e->getMessage());
        }
    }
}
