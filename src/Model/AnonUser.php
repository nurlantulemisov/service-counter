<?php

declare(strict_types=1);

namespace Nurlantulemisov\ServiceCounter\Model;

class AnonUser
{
    private const SALT = 'my-awesome-salt';

    private string $hash;

    public function __construct(string $ip, string $userAgent)
    {
        $this->hash = hash('sha256', $ip . $userAgent . self::SALT);
    }

    public function hash(): string
    {
        return $this->hash;
    }

    public function isEqual(string $other): bool
    {
        return $this->hash === $other;
    }
}
