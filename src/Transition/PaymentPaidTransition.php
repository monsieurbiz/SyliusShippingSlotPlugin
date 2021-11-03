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

namespace MonsieurBiz\SyliusShippingSlotPlugin\Transition;

use MonsieurBiz\SyliusShippingSlotPlugin\Entity\OrderInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShipmentInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Message\ShipmentMessage;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class PaymentPaidTransition
{
    private MessageBusInterface $eventBus;

    public function __construct(MessageBusInterface $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    public function __invoke(PaymentInterface $payment): void
    {
        if (!$this->shouldEventBeDispatched($payment)) {
            return;
        }

        /** @var OrderInterface $order */
        $order = $payment->getOrder();

        /** @var ShipmentInterface $shipment */
        foreach ($order->getShipments() as $shipment) {
            if (null !== $shipment->getSlot()) {
                $this->eventBus->dispatch(new ShipmentMessage($shipment->getId()));
            }
        }
    }

    private function shouldEventBeDispatched(PaymentInterface $payment): bool
    {
        /** @var OrderInterface $order */
        $order = $payment->getOrder();

        return null !== $order && \count($order->getSlots());
    }
}
