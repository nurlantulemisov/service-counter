<?php

declare(strict_types=1);

namespace Nurlantulemisov\ServiceCounter\Listner;

use Nurlantulemisov\ServiceCounter\Service\UserService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class UserListener
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $user = $this->userService->getAnonUser($event->getRequest());
        $event->getRequest()->headers->set('user-hash', $user->hash());
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        $hash = $event->getRequest()->headers->get('user-hash');
        if ($hash !== null) {
            $userHashCookie = new Cookie('user-hash', $hash);
            $response = $event->getResponse();
            $response->headers->setCookie($userHashCookie);
            $event->setResponse($response);
        }
    }
}
