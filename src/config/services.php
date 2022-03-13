<?php

use Nurlantulemisov\ServiceCounter\Controller\CounterController;
use Nurlantulemisov\ServiceCounter\Controller\TestController;
use Nurlantulemisov\ServiceCounter\Listner\UserListener;
use Nurlantulemisov\ServiceCounter\Service\CounterService;
use Nurlantulemisov\ServiceCounter\Service\TestService;
use Nurlantulemisov\ServiceCounter\Service\UserService;
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

    $container->register(UserService::class, UserService::class)
        ->setArguments([$cache]);

    $container->register(CounterService::class, CounterService::class)
        ->setArguments([$cache]);

    $container->register(UserListener::class, UserListener::class)
        ->addTag('kernel.event_listener', ['event' => 'kernel.request'])
        ->addTag('kernel.event_listener', ['event' => 'kernel.response'])
        ->setArguments([new Reference(UserService::class)]);

    $container->register(CounterController::class, CounterController::class)
        ->addTag('controller.service_arguments')
        ->setArguments([new Reference(CounterService::class)])
        ->setAutoconfigured(true)
        ->setPublic(true);
};
