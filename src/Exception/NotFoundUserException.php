<?php

namespace Nurlantulemisov\ServiceCounter\Exception;

use Exception;
use JetBrains\PhpStorm\Pure;
use Throwable;

class NotFoundUserException extends Exception
{
    private static string $defaultMessage = 'User not found';

    #[Pure] public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        if ($message === '') {
            $message = self::$defaultMessage;
        }

        parent::__construct($message, $code, $previous);
    }
}
