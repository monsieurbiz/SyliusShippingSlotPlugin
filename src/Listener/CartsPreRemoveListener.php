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

namespace MonsieurBiz\SyliusShippingSlotPlugin\Listener;

use Doctrine\ORM\EntityManagerInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\OrderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class CartsPreRemoveListener
{
    private EntityManagerInterface $slotManager;

    public function __construct(EntityManagerInterface $slotManager)
    {
        $this->slotManager = $slotManager;
    }

    public function removeExpiredCartsSlots(GenericEvent $event): void
    {
        $expiredCarts = $event->getSubject();

        /** @var OrderInterface $expiredCart */
        foreach ($expiredCarts as $expiredCart) {
            foreach ($expiredCart->getSlots() as $slot) {
                $this->slotManager->remove($slot);
            }
        }
    }
}
