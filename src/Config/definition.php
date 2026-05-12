<?php

use AntroninConsulting\PswExpirationBundle\Config\Unit;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

return static function (DefinitionConfigurator $definition): void {
    $definition->rootNode()->children()
        ->enumNode(name: 'unit')
        ->enumFqcn(enumFqcn: Unit::class)
        ->defaultValue(value: Unit::DAYS->value)
        ->info(info: 'The unit of time for password expiration.')->end()
        ->integerNode(name: 'lifetime')
        ->defaultValue(value: 90)
        ->info(info: 'Number of units after which a password expires.')
        ->end()
        ->integerNode(name: 'warning_threshold')
        ->defaultValue(value: 14)
        ->info(info: 'Number of units before expiration to start showing warnings.')
        ->end()
        ->end();
};
