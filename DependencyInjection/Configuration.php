<?php

/*
 * This file is part of the Vercom PHP API Client Symfony Bundle.
 *
 * (c) Mateusz Żyła <mateusz.zylaa@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plotkabytes\VercomApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('plotkabytes_vercom_api');

        $rootNode = $treeBuilder->getRootNode();

        $rootNode->children()
            ->arrayNode("clients")
                ->isRequired()
                ->requiresAtLeastOneElement()
                ->useAttributeAsKey('name')
                ->arrayPrototype()
                ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('authorization_key')->defaultValue(null)->isRequired()->end()
                        ->scalarNode('application_key')->defaultValue(null)->end()
                        ->scalarNode('alias')->defaultValue(null)->end()
                        ->booleanNode('default')->defaultValue(false)->end()
                        ->scalarNode('http_client')->defaultValue(null)->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}