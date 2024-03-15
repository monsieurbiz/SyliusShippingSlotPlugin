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

use MonsieurBiz\SyliusShippingSlotPlugin\Form\Type\ShippingSlotConfigsByMethod;
use Sylius\Bundle\CoreBundle\Form\Type\Checkout\ShipmentType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class ShipmentTypeExtension extends AbstractTypeExtension
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
                $form = $event->getForm();
                $shipment = $event->getData();

                $form->add('shippingSlotConfigs', ShippingSlotConfigsByMethod::class, [
                    'subject' => $shipment,
                    'mapped' => false, // This field is not mapped to the object, it's just for choice the shipping slot config to use
                ]);
            })
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        return [ShipmentType::class];
    }
}
