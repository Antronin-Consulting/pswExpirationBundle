<?php

namespace AntroninConsulting\PswExpirationBundle\DependencyInjection;

use AntroninConsulting\PswExpirationBundle\Config\Unit;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

class AntroninConsultingPswExpirationExtension extends AbstractBundle
{
  public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
  {

    $container->import(resource: '../config/services.yaml');

    $container->setParameter(name: 'psw_expiration.lifetime', value: $config['password_lifetime']);
    $container->setParameter(name: 'psw_expiration.warning_threshold', value: $config['warning_threshold']);
    $container->setParameter(name: 'psw_expiration.unit', value: Unit::from(value: $config['unit']));
  }

  public function configure(DefinitionConfigurator $definition): void
  {
    $definition->import(resource: 'Config/definition.php');
  }
}
