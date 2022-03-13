<?php

declare(strict_types=1);

namespace Nurlantulemisov\ServiceCounter\Controller;

use Nurlantulemisov\ServiceCounter\Service\CounterService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CounterController
{
    private CounterService $counterService;

    public function __construct(CounterService $counterService)
    {
        $this->counterService = $counterService;
    }

    public function count(Request $request): Response
    {
        return new Response('count: ' . $this->counterService->getCount($request->headers->get('user-hash')));
    }
}
