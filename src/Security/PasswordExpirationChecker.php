<?php

namespace AntroninConsulting\PswExpirationBundle\Security;

use AntroninConsulting\PswExpirationBundle\Config\Unit;
use DateTimeImmutable;

class PasswordExpirationChecker
{
    private int $passwordLifetime;
    private int $warningThreshold;
    private Unit $unit;

    public function __construct(int $passwordLifetime, int $warningThreshold, Unit $unit)
    {
        $this->passwordLifetime = $passwordLifetime;
        $this->warningThreshold = $warningThreshold;
        $this->unit = $unit;
    }

    public function isPasswordExpired(PasswordExpirationUserInterface $user): bool
    {
        /** @var \DateTime|null $lastChange */
        $lastChange = $user->getLastPasswordChange();
        if (null === $lastChange) {
            // If no last password change date is set, consider it not expired
            // or handle as per application's default policy (e.g., force change).
            // For this bundle, we assume a null means it's not tracked/expired by this mechanism.
            return false;
        }

        $expirationDate = $lastChange->modify(sprintf('+%d %s', $this->passwordLifetime, $this->unit->value));
        $now = new \DateTimeImmutable();

        return $now->getTimestamp() > $expirationDate->getTimestamp();
    }

    public function isPasswordNearingExpiration(PasswordExpirationUserInterface $user): bool
    {
        /** @var \DateTime|null $lastChange */
        $lastChange = $user->getLastPasswordChange();
        if (null === $lastChange) {
            return false;
        }

        $expirationDate = $lastChange->modify(sprintf('+%d %s', $this->passwordLifetime, $this->unit->value));
        $warningDate = $expirationDate->modify(sprintf('-%d %s', $this->warningThreshold, $this->unit->value));
        $now = new \DateTimeImmutable();

        return $now->getTimestamp() >= $warningDate->getTimestamp() && $now->getTimestamp() < $expirationDate->getTimestamp();
    }
}
