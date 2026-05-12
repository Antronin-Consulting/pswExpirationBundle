<?php

declare(strict_types=1);

namespace AntroninConsulting\PswExpirationBundle\Tests\Fixture;

use AntroninConsulting\PswExpirationBundle\PswExpirationBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new PswExpirationBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../config/config.yaml');
    }
}
