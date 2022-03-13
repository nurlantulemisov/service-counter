<?php

use Nurlantulemisov\ServiceCounter\Controller\CounterController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes) {
    $routes->add('stat', '/stat')
        ->controller([CounterController::class, 'stat'])
        ->methods(['GET']);

    $routes->add('update_count', '/count/{localeSlug}')
        ->controller([CounterController::class, 'update'])
        ->methods(['POST']);
};
