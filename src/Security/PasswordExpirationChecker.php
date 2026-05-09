<?php

namespace AntroninConsulting\PswExpirationBundle\Security;

class PasswordExpirationChecker
{
    private int $passwordLifetimeDays;
    private int $warningThresholdDays;

    public function __construct(int $passwordLifetimeDays, int $warningThresholdDays)
    {
        $this->passwordLifetimeDays = $passwordLifetimeDays;
        $this->warningThresholdDays = $warningThresholdDays;
    }

    public function isPasswordExpired(PasswordExpirationUserInterface $user): bool
    {
        $lastChange = $user->getLastPasswordChange();
        if (null === $lastChange) {
            // If no last password change date is set, consider it not expired
            // or handle as per application's default policy (e.g., force change).
            // For this bundle, we assume a null means it's not tracked/expired by this mechanism.
            return false;
        }

        $expirationDate = $lastChange->modify(sprintf('+%d days', $this->passwordLifetimeDays));
        $now = new \DateTimeImmutable();

        return $now > $expirationDate;
    }

    public function isPasswordNearingExpiration(PasswordExpirationUserInterface $user): bool
    {
        $lastChange = $user->getLastPasswordChange();
        if (null === $lastChange) {
            return false;
        }

        $expirationDate = $lastChange->modify(sprintf('+%d days', $this->passwordLifetimeDays));
        $warningDate = $expirationDate->modify(sprintf('-%d days', $this->warningThresholdDays));
        $now = new \DateTimeImmutable();

        return $now >= $warningDate && $now < $expirationDate;
    }
}
