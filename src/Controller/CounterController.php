<?php

declare(strict_types=1);

namespace Nurlantulemisov\ServiceCounter\Controller;

use DomainException;
use Nurlantulemisov\ServiceCounter\ReadModel\CountryStat;
use Nurlantulemisov\ServiceCounter\Service\CounterService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Countries;

class CounterController
{
    private CounterService $counterService;

    public function __construct(CounterService $counterService)
    {
        $this->counterService = $counterService;
    }

    public function stat(Request $request): Response
    {
        try {
            $stats = $this->counterService->getStats();
        } catch (DomainException $exception) {
            return new JsonResponse(['message' => 'Server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $response = [];
        array_walk($stats, static function (CountryStat $stat) use (&$response) {
            $response[$stat->getLocale()] = $stat->getCount();
        });
        return new JsonResponse($response);
    }

    public function update(Request $request, string $localeSlug): Response
    {
        $isValidLanguage = Countries::exists(mb_strtoupper($localeSlug));
        if (!$isValidLanguage) {
            return new JsonResponse(['message' => 'Country code not exist'], Response::HTTP_BAD_REQUEST);
        }
        try {
            $this->counterService->updateCount($localeSlug);
        } catch (DomainException $exception) {
            return new JsonResponse(['message' => 'Server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'OK']);
    }
}
