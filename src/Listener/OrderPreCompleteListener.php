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

namespace MonsieurBiz\SyliusShippingSlotPlugin\Listener;

use MonsieurBiz\SyliusShippingSlotPlugin\Entity\OrderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Webmozart\Assert\Assert;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\SlotInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Generator\SlotGeneratorInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShipmentInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShippingMethodInterface;

final class OrderPreCompleteListener
{
    private RouterInterface $router;
    private EntityManagerInterface $slotManager;
    private SlotGeneratorInterface $slotGenerator;

    public function __construct(
        RouterInterface $router,
        EntityManagerInterface $slotManager,
        SlotGeneratorInterface $slotGenerator
    ) {
        $this->router = $router;
        $this->slotManager = $slotManager;
        $this->slotGenerator = $slotGenerator;
    }

    public function checkSlot(ResourceControllerEvent $event): void
    {
        $order = $event->getSubject();

        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        $nonValidSlots = [];
        $missingSlots = [];
        /** @var ShipmentInterface $shipment */
        foreach ($order->getShipments() as $shipment) {
            /** @var ShippingMethodInterface $shippingMethod */
            $shippingMethod = $shipment->getMethod();
            if ($shippingMethod->getShippingSlotConfig()) {
                $slot = $shipment->getSlot();
                if (null === $slot) {
                    $missingSlots[] = $shippingMethod;
                } elseif (!$slot->isValid() || $this->slotGenerator->isFull($slot)) {
                    $nonValidSlots[] = $slot;
                    $this->slotManager->remove($slot);
                }
            }
        }

        if (empty($nonValidSlots) && empty($missingSlots)) {
            return;
        }

        $this->slotManager->flush();
        $event->stop(
            'monsieurbiz_shipping_slot.order.slot_no_longer_available',
            ResourceControllerEvent::TYPE_ERROR
        );
        $event->setResponse(new RedirectResponse($this->router->generate('sylius_shop_cart_summary')));
    }
}
