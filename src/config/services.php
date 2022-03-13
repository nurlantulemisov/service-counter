<?php

use Nurlantulemisov\ServiceCounter\Controller\CounterController;
use Nurlantulemisov\ServiceCounter\Service\CounterService;
use Nurlantulemisov\ServiceCounter\Service\CountSupplier;
use Nurlantulemisov\ServiceCounter\Service\CountUpdater;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\RedisTagAwareAdapter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventDispatcher;

return static function (ContainerBuilder $container) {
    $container->register('event_dispatcher', EventDispatcher::class)
        ->setPublic(true);

    $container
        ->register(RedisAdapter::class, RedisAdapter::class)
        ->setFactory([RedisAdapter::class, 'createConnection'])
        ->setArgument('$dsn', 'redis://redis');

    $container
        ->register(RedisTagAwareAdapter::class, RedisTagAwareAdapter::class)
        ->setArgument('$redis', new Reference(RedisAdapter::class));

    $cache = new Reference(RedisTagAwareAdapter::class);

    $container->register(CountUpdater::class, CountUpdater::class)
        ->setArguments([$cache])->setPublic(true);

    $container->register(CountSupplier::class, CountSupplier::class)
        ->setArguments([$cache])->setPublic(true);

    $container->register(CounterService::class, CounterService::class)
        ->setArguments([new Reference('service_container'), $cache]);

    $container->register(CounterController::class, CounterController::class)
        ->addTag('controller.service_arguments')
        ->setArguments([new Reference(CounterService::class)])
        ->setAutoconfigured(true)
        ->setPublic(true);

};
