<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ContainerControllerResolver;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Loader\PhpFileLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader as DIPhpFileLoader;

\Locale::setDefault('en'); // set default locale for symfony lib

// Load DI
$file = __DIR__ . '/tmp/di-cached/container.php';
$containerConfigCache = new ConfigCache($file, false);
if (!$containerConfigCache->isFresh()) {
    $containerBuilder = new ContainerBuilder();
    $containerBuilder->addCompilerPass(new RegisterListenersPass());
    $DILoader = new DIPhpFileLoader($containerBuilder, new FileLocator(__DIR__));
    try {
        $DILoader->load('src/config/services.php');
    } catch (Exception $e) {
        print_r($e->getMessage());
        exit;
    }

    $containerBuilder->compile();

    $dumper = new PhpDumper($containerBuilder);
    $containerConfigCache->write(
        $dumper->dump(['class' => 'MyCachedContainer']),
        $containerBuilder->getResources()
    );
}

require_once $file;
$container = new MyCachedContainer();

$request = Request::createFromGlobals();

$loader = new PhpFileLoader(new FileLocator(__DIR__));
$routes = $loader->load('src/config/routers.php');

$context = new RequestContext();
$context->fromRequest($request);
$matcher = new UrlMatcher($routes, $context);

/**
 * @var $dispatcher EventDispatcher
 */
$dispatcher = $container->get('event_dispatcher');

$controllerResolver = new ContainerControllerResolver($container);
$argumentResolver = new ArgumentResolver();

$requests = new RequestStack();
$request->attributes->add($matcher->match($request->getPathInfo()));
$requests->push($request);

$kernel = new HttpKernel($dispatcher, $controllerResolver, $requests, $argumentResolver);
try {
    $response = $kernel->handle($request);
} catch (Exception $exception) {
    $response = new Response($exception->getMessage(), 500);
}

// send the headers and echo the content
$response->send();

// trigger the kernel.terminate event
$kernel->terminate($request, $response);
