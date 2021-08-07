<?php

/*
 * This file is part of Monsieur Biz' Shipping Slot plugin for Sylius.
 *
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusShippingSlotPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('monsieurbiz_sylius_shipping_slot');
        $rootNode = method_exists($treeBuilder, 'getRootNode') ?
            $rootNode = $treeBuilder->getRootNode()
            : $treeBuilder->root('monsieurbiz_sylius_shipping_slot'); // BC layer for symfony/config 4.1 and older

        $rootNode
            ->children()
            ->arrayNode('expiration')
                ->children()
                    ->scalarNode('slot')->isRequired()->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
