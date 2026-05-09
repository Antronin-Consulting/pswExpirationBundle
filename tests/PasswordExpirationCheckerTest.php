<?php

namespace AntroninConsulting\PswExpirationBundle\Tests\Security;

use AntroninConsulting\PswExpirationBundle\Security\PasswordExpirationChecker;
use AntroninConsulting\PswExpirationBundle\Security\PasswordExpirationUserInterface;
use PHPUnit\Framework\TestCase;

class PasswordExpirationCheckerTest extends TestCase
{
    private const LIFETIME = 90;
    private const WARNING = 14;

    private PasswordExpirationChecker $checker;
    private PasswordExpirationUserInterface $user;

    protected function setUp(): void
    {
        $this->checker = new PasswordExpirationChecker(
            passwordLifetimeDays: self::LIFETIME,
            warningThresholdDays: self::WARNING
        );
        $this->user = $this->createStub(type: PasswordExpirationUserInterface::class);
    }

    public function testIsPasswordExpiredReturnsFalseWhenNoLastChangeDate(): void
    {
        $this->user->method(constraint: 'getLastPasswordChange')->willReturn(value: null);

        self::assertFalse(condition: $this->checker->isPasswordExpired(user: $this->user));
    }

    public function testIsPasswordExpiredReturnsTrueForExpiredPassword(): void
    {
        $lastChange = new \DateTimeImmutable(datetime: sprintf('-%d days', self::LIFETIME + 1));
        $this->user->method(constraint: 'getLastPasswordChange')->willReturn(value: $lastChange);

        self::assertTrue(condition: $this->checker->isPasswordExpired(user: $this->user));
    }

    public function testIsPasswordExpiredReturnsFalseForActivePassword(): void
    {
        $lastChange = new \DateTimeImmutable(datetime: '-10 days');
        $this->user->method(constraint: 'getLastPasswordChange')->willReturn(value: $lastChange);

        self::assertFalse(condition: $this->checker->isPasswordExpired(user: $this->user));
    }

    public function testIsPasswordNearingExpirationReturnsFalseWhenNoLastChangeDate(): void
    {
        $this->user->method(constraint: 'getLastPasswordChange')->willReturn(value: null);

        self::assertFalse(condition: $this->checker->isPasswordNearingExpiration(user: $this->user));
    }

    public function testIsPasswordNearingExpirationReturnsTrueWhenInWarningPeriod(): void
    {
        // Password expires in (LIFETIME - (LIFETIME - WARNING + 1)) = WARNING - 1 days
        $daysAgo = self::LIFETIME - self::WARNING + 1;
        $lastChange = new \DateTimeImmutable(datetime: sprintf('-%d days', $daysAgo));
        $this->user->method(constraint: 'getLastPasswordChange')->willReturn(value: $lastChange);

        self::assertTrue(condition: $this->checker->isPasswordNearingExpiration(user: $this->user));
    }

    public function testIsPasswordNearingExpirationReturnsFalseWhenBeforeWarningPeriod(): void
    {
        $lastChange = new \DateTimeImmutable(datetime: '-1 day');
        $this->user->method(constraint: 'getLastPasswordChange')->willReturn(value: $lastChange);

        self::assertFalse(condition: $this->checker->isPasswordNearingExpiration(user: $this->user));
    }

    public function testIsPasswordNearingExpirationReturnsFalseWhenPasswordIsExpired(): void
    {
        $lastChange = new \DateTimeImmutable(datetime: sprintf('-%d days', self::LIFETIME + 1));
        $this->user->method(constraint: 'getLastPasswordChange')->willReturn(value: $lastChange);

        self::assertFalse(condition: $this->checker->isPasswordNearingExpiration(user: $this->user));
    }

    public function testIsPasswordNearingExpirationOnWarningBoundary(): void
    {
        $daysAgo = self::LIFETIME - self::WARNING;
        $lastChange = new \DateTimeImmutable(datetime: sprintf('-%d days', $daysAgo));
        $this->user->method(constraint: 'getLastPasswordChange')->willReturn(value: $lastChange);

        self::assertTrue(condition: $this->checker->isPasswordNearingExpiration(user: $this->user));
    }
}
