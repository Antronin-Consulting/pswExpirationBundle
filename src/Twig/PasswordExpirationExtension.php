<?php

namespace AntroninConsulting\PswExpirationBundle\Twig;

use AntroninConsulting\PswExpirationBundle\Security\PasswordExpirationChecker;
use AntroninConsulting\PswExpirationBundle\Security\PasswordExpirationUserInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PasswordExpirationExtension extends AbstractExtension
{
    private PasswordExpirationChecker $passwordExpirationChecker;

    public function __construct(PasswordExpirationChecker $passwordExpirationChecker)
    {
        $this->passwordExpirationChecker = $passwordExpirationChecker;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_password_expired', [$this, 'isPasswordExpired']),
            new TwigFunction('is_password_nearing_expiration', [$this, 'isPasswordNearingExpiration']),
        ];
    }

    public function isPasswordExpired(?PasswordExpirationUserInterface $user): bool
    {
        if (null === $user) {
            return false;
        }

        return $this->passwordExpirationChecker->isPasswordExpired($user);
    }

    public function isPasswordNearingExpiration(?PasswordExpirationUserInterface $user): bool
    {
        if (null === $user) {
            return false;
        }

        return $this->passwordExpirationChecker->isPasswordNearingExpiration($user);
    }
}
