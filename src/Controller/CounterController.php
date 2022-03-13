<?php

declare(strict_types=1);

namespace Nurlantulemisov\ServiceCounter\Controller;

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

    public function count(Request $request): Response
    {
        $stats = $this->counterService->getStats();

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
        $this->counterService->updateCount($localeSlug);
        return new JsonResponse(['message' => 'OK']);
    }
}
