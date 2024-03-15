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

namespace MonsieurBiz\SyliusShippingSlotPlugin\Form\Type;

use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShipmentInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Resolver\ShippingSlotConfigResolverInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ShippingSlotConfigsByMethod extends AbstractType
{
    private ShippingMethodsResolverInterface $shippingMethodsResolver;

    private RepositoryInterface $repository;

    private ShippingSlotConfigResolverInterface $shippingSlotConfigResolver;

    public function __construct(
        ShippingMethodsResolverInterface $shippingMethodsResolver,
        RepositoryInterface $repository,
        ShippingSlotConfigResolverInterface $shippingSlotConfigResolver
    ) {
        $this->shippingMethodsResolver = $shippingMethodsResolver;
        $this->repository = $repository;
        $this->shippingSlotConfigResolver = $shippingSlotConfigResolver;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $subject = $options['subject'] ?? null;
        $currentShippingSlotConfig = $this->shippingSlotConfigResolver->getShippingSlotConfig($subject);
        $currentShippingMethod = $this->getCurrentShippingMethod($subject);
        foreach ($this->getShippingMethods($subject) as $shippingMethod) {
            $builder->add($shippingMethod->getCode(), ChoiceType::class, [
                'choices' => $shippingMethod->getShippingSlotConfigs(),
                'choice_label' => 'name',
                'choice_value' => 'id',
                'label' => false,
                'data' => $currentShippingMethod === $shippingMethod ? $currentShippingSlotConfig : null,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefined([
                'subject',
            ])
            ->setAllowedTypes('subject', ShippingSubjectInterface::class)
        ;
    }

    private function getShippingMethods(?ShippingSubjectInterface $subject): array
    {
        if (null !== $subject && $this->shippingMethodsResolver->supports($subject)) {
            return $this->shippingMethodsResolver->getSupportedMethods($subject);
        }

        return $this->repository->findAll();
    }

    private function getCurrentShippingMethod(?ShippingSubjectInterface $subject): ?ShippingMethodInterface
    {
        if (!$subject instanceof ShipmentInterface) {
            return null;
        }

        return $subject->getMethod();
    }
}
