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

namespace MonsieurBiz\SyliusShippingSlotPlugin\Fixture\Factory;

use Faker\Factory;
use Faker\Generator;
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShippingMethodInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShippingSlotConfigInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShippingSlotConfigFixtureFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    private FactoryInterface $shippingSlotConfigFactory;
    private RepositoryInterface $shippingMethodRepository;
    private OptionsResolver $optionsResolver;
    private Generator $faker;

    public function __construct(
        FactoryInterface $shippingSlotConfigFactory,
        RepositoryInterface $shippingMethodRepository
    ) {
        $this->shippingSlotConfigFactory = $shippingSlotConfigFactory;
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->faker = Factory::create();
        $this->optionsResolver = new OptionsResolver();
        $this->configureOptions($this->optionsResolver);
    }

    public function create(array $options = []): ShippingSlotConfigInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var ShippingSlotConfigInterface $shippingSlotConfig */
        $shippingSlotConfig = $this->shippingSlotConfigFactory->createNew();
        $shippingSlotConfig->setName($options['name']);
        $shippingSlotConfig->setTimezone($options['timezone']);
        $shippingSlotConfig->setRrules($options['rrules']);
        $shippingSlotConfig->setPreparationDelay($options['preparationDelay']);
        $shippingSlotConfig->setPickupDelay($options['pickupDelay']);
        $shippingSlotConfig->setDurationRange($options['durationRange']);
        $shippingSlotConfig->setAvailableSpots($options['availableSpots']);
        $shippingSlotConfig->setColor($options['color']);

        /** @var ShippingMethodInterface $shippingMethod */
        foreach ($options['shipping_methods'] as $shippingMethod) {
            $shippingMethod->setShippingSlotConfig($shippingSlotConfig);
        }

        return $shippingSlotConfig;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('name', function(): string {
                return $this->faker->sentence(2, true);
            })
            ->setDefault('timezone', function(): string {
                return $this->faker->timezone;
            })
            ->setDefault('rrules', function(): array {
                return [
                    'RRULE:FREQ=HOURLY;INTERVAL=1;WKST=MO;BYDAY=MO,TU,WE,TH,FR;BYMONTH=9,10,11;BYHOUR=8,9,10,11,12,13,14,15,16,17,18;BYMINUTE=0;BYSECOND=0',
                    'RRULE:FREQ=HOURLY;INTERVAL=1;WKST=MO;BYDAY=MO,TU,WE,TH,FR;BYMONTH=9,10,11;BYHOUR=8,9,10,11,12,13,14,15,16,17,18;BYMINUTE=30;BYSECOND=0',
                ];
            })
            ->setDefault('preparationDelay', function(): int {
                return $this->faker->numberBetween(3, 12) * 10;
            })
            ->setDefault('pickupDelay', function(): int {
                return $this->faker->numberBetween(3, 12) * 10;
            })
            ->setDefault('durationRange', function(): int {
                return $this->faker->numberBetween(2, 4) * 60;
            })
            ->setDefault('availableSpots', function(): int {
                return $this->faker->numberBetween(5, 10);
            })
            ->setDefault('color', function(): string {
                return $this->faker->hexColor;
            })
            ->setDefault('shipping_methods', [])
            ->setAllowedTypes('shipping_methods', 'array')
            ->setNormalizer('shipping_methods', LazyOption::findBy($this->shippingMethodRepository, 'code'))
        ;
    }
}
