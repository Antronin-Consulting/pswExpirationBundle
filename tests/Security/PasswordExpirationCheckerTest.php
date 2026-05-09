<?php

namespace AntroninConsulting\PswExpirationBundle\Tests\Security;

use AntroninConsulting\PswExpirationBundle\Config\Unit;
use AntroninConsulting\PswExpirationBundle\Security\PasswordExpirationChecker;
use AntroninConsulting\PswExpirationBundle\Security\PasswordExpirationUserInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PasswordExpirationCheckerTest extends TestCase
{
    private const LIFETIME = 90;
    private const WARNING = 14;

    private PasswordExpirationUserInterface $user;

    protected function setUp(): void
    {
        $this->user = $this->createStub(type: PasswordExpirationUserInterface::class);
    }

    public static function unitProvider(): array
    {
        return [
            [Unit::HOURS],
            [Unit::DAYS],
            [Unit::WEEKS],
            [Unit::MONTHS],
        ];
    }

    public function testIsPasswordExpiredReturnsFalseWhenNoLastChangeDate(): void
    {
        $checker = new PasswordExpirationChecker(passwordLifetime: self::LIFETIME, warningThreshold: self::WARNING, unit: Unit::DAYS);
        $this->user->method(constraint: 'getLastPasswordChange')->willReturn(value: null);

        self::assertFalse(condition: $checker->isPasswordExpired(user: $this->user));
    }

    #[DataProvider(methodName: 'unitProvider')]
    public function testIsPasswordExpiredReturnsTrueForExpiredPassword(Unit $unit): void
    {
        $checker = new PasswordExpirationChecker(self::LIFETIME, self::WARNING, $unit);
        $lastChange = new \DateTimeImmutable(datetime: sprintf('-%d %s', self::LIFETIME + 1, $unit->value));
        $this->user->method(constraint: 'getLastPasswordChange')->willReturn(value: $lastChange);

        self::assertTrue(condition: $checker->isPasswordExpired(user: $this->user));
    }

    #[DataProvider(methodName: 'unitProvider')]
    public function testIsPasswordExpiredReturnsFalseForActivePassword(Unit $unit): void
    {
        $checker = new PasswordExpirationChecker(self::LIFETIME, self::WARNING, $unit);
        $lastChange = new \DateTimeImmutable(datetime: sprintf('-10 %s', $unit->value));
        $this->user->method(constraint: 'getLastPasswordChange')->willReturn(value: $lastChange);

        self::assertFalse(condition: $checker->isPasswordExpired(user: $this->user));
    }

    public function testIsPasswordNearingExpirationReturnsFalseWhenNoLastChangeDate(): void
    {
        $checker = new PasswordExpirationChecker(self::LIFETIME, self::WARNING, Unit::DAYS);
        $this->user->method('getLastPasswordChange')->willReturn(null);

        self::assertFalse($checker->isPasswordNearingExpiration($this->user));
    }

    #[DataProvider(methodName: 'unitProvider')]
    public function testIsPasswordNearingExpirationReturnsTrueWhenInWarningPeriod(Unit $unit): void
    {
        $checker = new PasswordExpirationChecker(self::LIFETIME, self::WARNING, $unit);
        $daysAgo = self::LIFETIME - self::WARNING + 1;
        $lastChange = new \DateTimeImmutable(datetime: sprintf('-%d %s', $daysAgo, $unit->value));
        $this->user->method(constraint: 'getLastPasswordChange')->willReturn(value: $lastChange);

        self::assertTrue(condition: $checker->isPasswordNearingExpiration(user: $this->user));
    }

    #[DataProvider(methodName: 'unitProvider')]
    public function testIsPasswordNearingExpirationReturnsFalseWhenBeforeWarningPeriod(Unit $unit): void
    {
        $checker = new PasswordExpirationChecker(self::LIFETIME, self::WARNING, $unit);
        $lastChange = new \DateTimeImmutable(datetime: sprintf('-1 %s', $unit->value));
        $this->user->method(constraint: 'getLastPasswordChange')->willReturn(value: $lastChange);

        self::assertFalse(condition: $checker->isPasswordNearingExpiration(user: $this->user));
    }

    #[DataProvider(methodName: 'unitProvider')]
    public function testIsPasswordNearingExpirationReturnsFalseWhenPasswordIsExpired(Unit $unit): void
    {
        $checker = new PasswordExpirationChecker(self::LIFETIME, self::WARNING, $unit);
        $lastChange = new \DateTimeImmutable(datetime: sprintf('-%d %s', self::LIFETIME + 1, $unit->value));
        $this->user->method(constraint: 'getLastPasswordChange')->willReturn(value: $lastChange);

        self::assertFalse(condition: $checker->isPasswordNearingExpiration(user: $this->user));
    }

    #[DataProvider(methodName: 'unitProvider')]
    public function testIsPasswordNearingExpirationOnWarningBoundary(Unit $unit): void
    {
        $checker = new PasswordExpirationChecker(self::LIFETIME, self::WARNING, $unit);
        $daysAgo = self::LIFETIME - self::WARNING;
        $lastChange = new \DateTimeImmutable(datetime: sprintf('-%d %s', $daysAgo, $unit->value));
        $this->user->method(constraint: 'getLastPasswordChange')->willReturn(value: $lastChange);

        self::assertTrue(condition: $checker->isPasswordNearingExpiration(user: $this->user));
    }
}
