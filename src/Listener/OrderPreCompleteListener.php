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

use Doctrine\ORM\EntityManagerInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\OrderInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShipmentInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShippingMethodInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Generator\SlotGeneratorInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

final class OrderPreCompleteListener
{
    private RouterInterface $router;
    private EntityManagerInterface $slotManager;
    private SlotGeneratorInterface $slotGenerator;
    private array $nonValidSlots;
    private array $missingSlots;

    public function __construct(
        RouterInterface $router,
        EntityManagerInterface $slotManager,
        SlotGeneratorInterface $slotGenerator
    ) {
        $this->router = $router;
        $this->slotManager = $slotManager;
        $this->slotGenerator = $slotGenerator;
        $this->nonValidSlots = [];
        $this->missingSlots = [];
    }

    public function checkSlot(ResourceControllerEvent $event): void
    {
        $order = $event->getSubject();

        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        /** @var ShipmentInterface $shipment */
        foreach ($order->getShipments() as $shipment) {
            $this->checkShipment($shipment);
        }

        if (empty($this->nonValidSlots) && empty($this->missingSlots)) {
            return;
        }

        $this->slotManager->flush();
        $event->stop(
            'monsieurbiz_shipping_slot.order.slot_no_longer_available',
            ResourceControllerEvent::TYPE_ERROR
        );
        $event->setResponse(new RedirectResponse($this->router->generate('sylius_shop_cart_summary')));
    }

    private function checkShipment(ShipmentInterface $shipment): void
    {
        /** @var ShippingMethodInterface|null $shippingMethod */
        $shippingMethod = $shipment->getMethod();
        Assert::isInstanceOf($shippingMethod, ShippingMethodInterface::class);
        if (!$shippingMethod->getShippingSlotConfig()) {
            return;
        }

        $slot = $shipment->getSlot();
        switch (true) {
            case null === $slot:
                $this->missingSlots[] = $shippingMethod;
                break;
            case !$slot->isValid() || $this->slotGenerator->isFull($slot):
                $this->nonValidSlots[] = $slot;
                $this->slotManager->remove($slot);
                break;
        }
    }
}
