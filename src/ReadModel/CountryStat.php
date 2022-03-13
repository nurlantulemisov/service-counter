<?php

namespace Nurlantulemisov\ServiceCounter\ReadModel;

class CountryStat
{
    private string $locale;

    private int $count;

    public function __construct(string $locale, int $count)
    {
        $this->locale = $locale;
        $this->count = $count;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
