<?php

use Nurlantulemisov\ServiceCounter\Controller\CounterController;
use Nurlantulemisov\ServiceCounter\Controller\TestController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes) {
    $routes->add('blog_list', '/blog')
        ->controller([TestController::class, 'list']);

    $routes->add('blog_get', '/blog/{id}')
        ->controller([TestController::class, 'get'])
        ->methods(['GET', 'HEAD']);

    $routes->add('count', '/count')
        ->controller([CounterController::class, 'count'])
        ->methods(['GET']);
};
