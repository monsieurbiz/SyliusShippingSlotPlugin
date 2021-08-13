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
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ProductVariantInterface;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

final class ProductVariantShippingSlotFixture extends AbstractFixture implements FixtureInterface
{
    private ProductVariantRepositoryInterface $productVariantRepository;
    private EntityManagerInterface $productVariantManager;

    public function __construct(
        ProductVariantRepositoryInterface $productVariantRepository,
        EntityManagerInterface $productVariantManager
    ) {
        $this->productVariantRepository = $productVariantRepository;
        $this->productVariantManager = $productVariantManager;
    }

    /**
     * @param array $options
     */
    public function load(array $options): void
    {
        foreach ($options['product_variants'] ?? [] as $option) {
            /** @var ProductVariantInterface $productVariant */
            if (null === ($productVariant = $this->productVariantRepository->findOneBy(['code' => $option['code'] ?? '']))) {
                continue;
            }

            $productVariant->setPreparationDelay($option['preparationDelay'] ?? null);
            $this->productVariantManager->persist($productVariant);
        }

        $this->productVariantManager->flush();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'monsieurbiz_shipping_slot_product_variant';
    }

    /**
     * @param ArrayNodeDefinition $optionsNode
     */
    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        /** @phpstan-ignore-next-line */
        $optionsNode
            ->children()
                ->arrayNode('product_variants')
                    ->arrayPrototype()
                    ->children()
                        ->scalarNode('code')->cannotBeEmpty()->end()
                        ->integerNode('preparationDelay')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
