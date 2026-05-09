<?php

namespace AntroninConsulting\PswExpirationBundle\Security;

/**
 * Interface for users whose passwords can expire.
 */
interface PasswordExpirationUserInterface
{
    public function getLastPasswordChange(): ?\DateTimeInterface;

    public function setLastPasswordChange(\DateTimeInterface $lastPasswordChange): void;
}
