<?php

namespace AntroninConsulting\PswExpirationBundle\Security;

use DateTimeInterface;

/**
 * Interface for users whose passwords can expire.
 */
interface PasswordExpirationUserInterface
{
    public function getLastPasswordChange(): ?DateTimeInterface;

    public function setLastPasswordChange(DateTimeInterface $lastPasswordChange): void;
}
