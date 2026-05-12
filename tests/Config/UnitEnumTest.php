<?php

namespace AntroninConsulting\PswExpirationBundle\Tests\Config;

use AntroninConsulting\PswExpirationBundle\Config\Unit;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class UnitEnumTest extends TestCase
{
    /**
     * @return array <int, array{Unit, string}>
     */
    public static function unitLabelProvider(): array
    {
        return [
            [Unit::HOURS,  'unit.hours'],
            [Unit::DAYS,   'unit.days'],
            [Unit::WEEKS,  'unit.weeks'],
            [Unit::MONTHS, 'unit.months'],
        ];
    }

    #[DataProvider('unitLabelProvider')]
    public function testLabel(Unit $unit, string $expectedLabel): void
    {
        self::assertSame($expectedLabel, $unit->label());
    }

    public function testTrans(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->expects($this->once())
            ->method('trans')
            ->with(
                'unit.days',
                [],
                'PswExpirationBundle',
                'fr'
            )
            ->willReturn('jours');

        $translated = Unit::DAYS->trans(translator: $translator, locale: 'fr');
        self::assertSame('jours', $translated);
    }
}
