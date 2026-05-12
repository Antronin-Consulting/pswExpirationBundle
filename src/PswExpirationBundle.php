<?php

/**
 * File: src\PswExpirationBundle.php
 * Author: Peter Nagy <peter@antronin.consulting>
 * -----.
 */

declare(strict_types=1);

namespace AntroninConsulting\PswExpirationBundle;

use AntroninConsulting\PswExpirationBundle\Config\Unit;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class PswExpirationBundle extends AbstractBundle
{
    /**
     * @param array<string, mixed> $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import(resource: '../config/services.yaml');

        $builder->setParameter(name: 'psw_expiration.lifetime', value: $config['lifetime']);
        $builder->setParameter(name: 'psw_expiration.warning_threshold', value: $config['warning_threshold']);
        $builder->setParameter(name: 'psw_expiration.unit', value: $config['unit']);
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->import(resource: 'Config/definition.php');
    }
}
