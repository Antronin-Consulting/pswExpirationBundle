<?php

namespace AntroninConsulting\PswExpirationBundle\Tests\Security;

use AntroninConsulting\PswExpirationBundle\Config\Unit;
use AntroninConsulting\PswExpirationBundle\Security\PasswordExpirationChecker;
use AntroninConsulting\PswExpirationBundle\Security\PasswordExpirationUserInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PasswordExpirationCheckerTest extends TestCase
{
    private PasswordExpirationUserInterface $user;

    protected function setUp(): void
    {
        $this->user = $this->createStub(type: PasswordExpirationUserInterface::class);
    }

    #[DataProvider(methodName: 'expiredProvider')]
    public function testIsPasswordExpired(int $lifetime, string $lastChange, Unit $unit, bool $expected): void
    {
        $this->user->method('getLastPasswordChange')->willReturn(new \DateTimeImmutable(datetime: $lastChange));
        $checker = new PasswordExpirationChecker(passwordLifetime: $lifetime, warningThreshold: 14, unit: $unit);
        self::assertSame(expected: $expected, actual: $checker->isPasswordExpired(user: $this->user));
    }

    public static function expiredProvider(): array
    {
        return [
            'expired days' => [90, '-91 days', Unit::DAYS, true],
            'not expired days' => [90, '-89 days', Unit::DAYS, false],
            'expired edge days' => [90, '-90 days -1 second', Unit::DAYS, true],
            'not expired edge days' => [90, '-90 days', Unit::DAYS, false],
            'expired months' => [3, '-4 months', Unit::MONTHS, true],
            'not expired months' => [3, '-2 months', Unit::MONTHS, false],
        ];
    }

    public function testIsPasswordExpiredWithNullLastChange(): void
    {
        $this->user->method('getLastPasswordChange')->willReturn(null);
        $checker = new PasswordExpirationChecker(passwordLifetime: 90, warningThreshold: 14, unit: Unit::DAYS);
        self::assertFalse($checker->isPasswordExpired(user: $this->user));
    }

    #[DataProvider(methodName: 'nearingExpirationProvider')]
    public function testIsPasswordNearingExpiration(int $lifetime, int $warning, string $lastChange, Unit $unit, bool $expected): void
    {
        $this->user->method('getLastPasswordChange')->willReturn(new \DateTimeImmutable($lastChange));
        $checker = new PasswordExpirationChecker(passwordLifetime: $lifetime, warningThreshold: $warning, unit: $unit);
        self::assertSame(expected: $expected, actual: $checker->isPasswordNearingExpiration(user: $this->user));
    }

    public static function nearingExpirationProvider(): array
    {
        return [
            'not nearing yet' => [90, 14, '-10 days', Unit::DAYS, false],
            'nearing' => [90, 14, '-80 days', Unit::DAYS, true],
            'expired' => [90, 14, '-100 days', Unit::DAYS, false],
            'nearing on warning threshold edge' => [90, 14, '-76 days', Unit::DAYS, true],
            'not nearing just before warning threshold' => [90, 14, '-75 days', Unit::DAYS, false],
            'nearing on expiration edge' => [90, 14, '-89 days', Unit::DAYS, true],
            'exactly expired' => [90, 14, '-90 days', Unit::DAYS, false],
        ];
    }

    public function testIsPasswordNearingExpirationWithNullLastChange(): void
    {
        $this->user->method('getLastPasswordChange')->willReturn(null);
        $checker = new PasswordExpirationChecker(passwordLifetime: 90, warningThreshold: 14, unit: Unit::DAYS);
        self::assertFalse($checker->isPasswordNearingExpiration(user: $this->user));
    }
}
