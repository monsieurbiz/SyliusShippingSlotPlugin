<?php

/*
 * This file is part of Monsieur Biz' Shipping Slot plugin for Sylius.
 *
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusShippingSlotPlugin\Form\Extension;

use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShippingMethodInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Form\Type\ShippingSlotConfigChoiceType;
use Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;

class ShippingMethodTypeExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [ShippingMethodType::class];
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('shippingSlotConfigs', ShippingSlotConfigChoiceType::class, [
                'required' => false,
                'label' => 'monsieurbiz_shipping_slot.ui.form.shipping_slot_config',
                'multiple' => true,
                'expanded' => true,
                'empty_data' => [],
                'constraints' => [
                    new Assert\Valid(),
                ],
            ])
            // Add the shipping slot config from the old attribute to the shipping method if it's not already set
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
                $shippingMethod = $event->getData();
                if (!$shippingMethod instanceof ShippingMethodInterface) {
                    return;
                }

                $oldShippingSlotConfig = $shippingMethod->getShippingSlotConfig();
                if (!$shippingMethod->getShippingSlotConfigs()->isEmpty() || null === $oldShippingSlotConfig) {
                    return;
                }

                $shippingMethod->addShippingSlotConfig($oldShippingSlotConfig);
            })
        ;
    }
}
