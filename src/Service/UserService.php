<?php

declare(strict_types=1);

namespace Nurlantulemisov\ServiceCounter\Service;

use Nurlantulemisov\ServiceCounter\Exception\NotFoundUserException;
use Nurlantulemisov\ServiceCounter\Model\AnonUser;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;

class UserService
{
    private CacheItemPoolInterface $cache;

    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }

    public function getAnonUser(Request $request): AnonUser
    {
        $ipAddress = $request->getClientIp();
        $userAgent = $request->headers->get('User-Agent');
        $cookie = $request->cookies->get('user-hash');

        try {
            if ($cookie !== null) {
                return $this->cachedUser($cookie, $userAgent, $ipAddress);
            }
        } catch (NotFoundUserException $exception) {
        }
        return new AnonUser($ipAddress, $userAgent);
    }

    /**
     * @throws NotFoundUserException
     * @throws InvalidArgumentException
     */
    private function cachedUser(string $cookie, string $userAgent, string $ipAddress): AnonUser
    {
        $cacheItem = $this->cache->getItem($cookie);
        if ($cacheItem->isHit()) {
            [$cachedIp, $cachedUserAgent] = $cacheItem->get();
            if ($cachedIp === $ipAddress && $cachedUserAgent === $userAgent) {
                return new AnonUser($ipAddress, $userAgent);
            }
        }

        $cacheItem = $cacheItem->set([$ipAddress, $userAgent]);
        $this->cache->save($cacheItem);
        throw new NotFoundUserException();
    }
}
