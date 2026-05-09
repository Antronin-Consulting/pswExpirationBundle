<?php

declare(strict_types=1);

namespace AntroninConsulting\PswExpirationBundle\Config;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum Unit: string implements TranslatableInterface
{
    case HOURS = 'hours';
    case DAYS = 'days';
    case WEEKS = 'weeks';
    case MONTHS = 'months';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans(id: $this->label(), parameters: [], domain: 'PswExpirationBundle', locale: $locale);
    }

    public function label(): string
    {
        return match ($this) {
            self::HOURS => 'unit.hours',
            self::DAYS => 'unit.days',
            self::WEEKS => 'unit.weeks',
            self::MONTHS => 'unit.months',
        };
    }
}
