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
        $this->user = $this->createStub(PasswordExpirationUserInterface::class);
    }

    public function testGetFunctions(): void
    {
        $this->checker = $this->createStub(PasswordExpirationChecker::class);
        $this->extension = new PasswordExpirationExtension(passwordExpirationChecker: $this->checker);
        $functions = $this->extension->getFunctions();
        self::assertCount(2, $functions);
        self::assertInstanceOf(TwigFunction::class, $functions[0]);
        self::assertInstanceOf(TwigFunction::class, $functions[1]);
        self::assertEquals('is_password_expired', $functions[0]->getName());
        self::assertEquals('is_password_nearing_expiration',  $functions[1]->getName());
    }

    public function testIsPasswordExpired(): void
    {
        $this->checker = $this->createMock(PasswordExpirationChecker::class);
        $this->checker->expects($this->once())
            ->method('isPasswordExpired')
            ->with(user: $this->user)
            ->willReturn(true);
        $this->extension = new PasswordExpirationExtension(passwordExpirationChecker: $this->checker);

        self::assertTrue($this->extension->isPasswordExpired($this->user));
    }

    public function testIsPasswordExpiredWithNullUser(): void
    {
        $this->checker = $this->createMock(PasswordExpirationChecker::class);
        $this->checker->expects($this->never())->method('isPasswordExpired');
        $this->extension = new PasswordExpirationExtension(passwordExpirationChecker: $this->checker);

        self::assertFalse($this->extension->isPasswordExpired(user: null));
    }

    public function testIsPasswordNearingExpiration(): void
    {
        $this->checker = $this->createMock(PasswordExpirationChecker::class);
        $this->checker->expects($this->once())
            ->method('isPasswordNearingExpiration')
            ->with(user: $this->user)
            ->willReturn(true);
        $this->extension = new PasswordExpirationExtension(passwordExpirationChecker: $this->checker);

        self::assertTrue($this->extension->isPasswordNearingExpiration(user: $this->user));
    }

    public function testIsPasswordNearingExpirationWithNullUser(): void
    {
        $this->checker = $this->createMock(PasswordExpirationChecker::class);
        $this->checker->expects($this->never())->method('isPasswordNearingExpiration');
        $this->extension = new PasswordExpirationExtension(passwordExpirationChecker: $this->checker);

        self::assertFalse($this->extension->isPasswordNearingExpiration(user: null));
    }
}
