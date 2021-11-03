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

namespace MonsieurBiz\SyliusShippingSlotPlugin\Fixture;

use Doctrine\ORM\EntityManagerInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Fixture\Factory\ShippingSlotConfigFixtureFactory;
use Sylius\Bundle\CoreBundle\Fixture\AbstractResourceFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class ShippingSlotConfigFixture extends AbstractResourceFixture
{
    public function __construct(
        EntityManagerInterface $shippingSlotConfigManager,
        ShippingSlotConfigFixtureFactory $exampleFactory
    ) {
        parent::__construct($shippingSlotConfigManager, $exampleFactory);
    }

    public function getName(): string
    {
        return 'monsieurbiz_shipping_slot_config';
    }

    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        /** @phpstan-ignore-next-line */
        $resourceNode
            ->children()
                ->scalarNode('name')->cannotBeEmpty()->end()
                ->scalarNode('timezone')->cannotBeEmpty()->end()
                ->arrayNode('rrules')
                    ->scalarPrototype()->end()
                ->end()
                ->integerNode('preparationDelay')->min(0)->end()
                ->integerNode('pickupDelay')->min(0)->end()
                ->integerNode('durationRange')->min(0)->end()
                ->integerNode('availableSpots')->min(1)->end()
                ->scalarNode('color')->cannotBeEmpty()->end()
                ->arrayNode('shipping_methods')
                    ->scalarPrototype()->end()
                ->end()
            ->end()
        ;
    }
}
