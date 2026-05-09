<?php

namespace AntroninConsulting\PswExpirationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(name: 'antronin_consulting_psw_expiration');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            ->integerNode(name: 'password_lifetime_days')
            ->defaultValue(value: 90)
            ->info(info: 'Number of days after which a password expires.')
            ->end()
            ->integerNode(name: 'warning_threshold_days')
            ->defaultValue(value: 14)
            ->info(info: 'Number of days before expiration to start showing warnings.')
            ->end()
            ->end();

        return $treeBuilder;
    }
}
