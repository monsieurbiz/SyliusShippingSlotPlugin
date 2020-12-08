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

namespace MonsieurBiz\SyliusShippingSlotPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ShippingSlotConfigType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'monsieurbiz_shipping_slot.ui.form.name',
            ])
            ->add('rrules', CollectionType::class, [
                'label' => 'monsieurbiz_shipping_slot.ui.form.rrules',
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('preparationDelay', IntegerType::class, [
                'label' => 'monsieurbiz_shipping_slot.ui.form.preparation_delay',
            ])
            ->add('pickupDelay', IntegerType::class, [
                'label' => 'monsieurbiz_shipping_slot.ui.form.pickup_delay',
            ])
            ->add('durationRange', IntegerType::class, [
                'label' => 'monsieurbiz_shipping_slot.ui.form.duration_range',
            ])
            ->add('availableSpots', IntegerType::class, [
                'label' => 'monsieurbiz_shipping_slot.ui.form.available_spots',
            ])
            ->add('color', ColorType::class, [
                'label' => 'monsieurbiz_shipping_slot.ui.form.color',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'monsieurbiz_shipping_slot_config';
    }
}
