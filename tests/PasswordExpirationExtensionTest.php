<?php

namespace AntroninConsulting\PswExpirationBundle\Tests\Twig;

use AntroninConsulting\PswExpirationBundle\Security\PasswordExpirationChecker;
use AntroninConsulting\PswExpirationBundle\Security\PasswordExpirationUserInterface;
use AntroninConsulting\PswExpirationBundle\Twig\PasswordExpirationExtension;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

class PasswordExpirationExtensionTest extends TestCase
{
    private PasswordExpirationChecker $checker;
    private PasswordExpirationExtension $extension;
    private PasswordExpirationUserInterface $user;

    protected function setUp(): void
    {
        $this->user = $this->createStub(type: PasswordExpirationUserInterface::class);
    }

    public function testGetFunctions(): void
    {
        $this->checker = $this->createStub(type: PasswordExpirationChecker::class);
        $this->extension = new PasswordExpirationExtension(passwordExpirationChecker: $this->checker);
        $functions = $this->extension->getFunctions();
        self::assertCount(expectedCount: 2, haystack: $functions);
        self::assertInstanceOf(expected: TwigFunction::class, actual: $functions[0]);
        self::assertInstanceOf(expected: TwigFunction::class, actual: $functions[1]);
        self::assertEquals(expected: 'is_password_expired', actual: $functions[0]->getName());
        self::assertEquals(expected: 'is_password_nearing_expiration', actual: $functions[1]->getName());
    }

    public function testIsPasswordExpired(): void
    {
        $this->checker = $this->createMock(type: PasswordExpirationChecker::class);
        $this->checker->expects($this->once())
            ->method('isPasswordExpired')
            ->with(user: $this->user)
            ->willReturn(value: true);
        $this->extension = new PasswordExpirationExtension(passwordExpirationChecker: $this->checker);

        self::assertTrue(condition: $this->extension->isPasswordExpired(user: $this->user));
    }

    public function testIsPasswordExpiredWithNullUser(): void
    {
        $this->checker = $this->createMock(type: PasswordExpirationChecker::class);
        $this->checker->expects($this->never())->method('isPasswordExpired');
        $this->extension = new PasswordExpirationExtension(passwordExpirationChecker: $this->checker);

        self::assertFalse(condition: $this->extension->isPasswordExpired(user: null));
    }

    public function testIsPasswordNearingExpiration(): void
    {
        $this->checker = $this->createMock(type: PasswordExpirationChecker::class);
        $this->checker->expects($this->once())
            ->method('isPasswordNearingExpiration')
            ->with(user: $this->user)
            ->willReturn(value: true);
        $this->extension = new PasswordExpirationExtension(passwordExpirationChecker: $this->checker);

        self::assertTrue(condition: $this->extension->isPasswordNearingExpiration(user: $this->user));
    }
}
