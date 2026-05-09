<?php

namespace AntroninConsulting\PswExpirationBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class AntroninConsultingPswExpirationExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration(configuration: $configuration, configs: $configs);

        $loader = new YamlFileLoader(
            container: $container,
            locator: new FileLocator(paths: __DIR__ . '/../Resources/config')
        );
        $loader->load(resource: 'services.yaml');

        $container->setParameter(name: 'antronin_consulting_psw_expiration.password_lifetime_days', value: $config['password_lifetime_days']);
        $container->setParameter(name: 'antronin_consulting_psw_expiration.warning_threshold_days', value: $config['warning_threshold_days']);
    }
}
