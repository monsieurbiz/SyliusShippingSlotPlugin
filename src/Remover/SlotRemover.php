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

namespace MonsieurBiz\SyliusShippingSlotPlugin\Remover;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

class SlotRemover implements SlotRemoverInterface
{
    private OrderRepositoryInterface $orderRepository;
    private EntityManagerInterface $slotManager;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param EntityManagerInterface $slotManager
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        EntityManagerInterface $slotManager
    ) {
        $this->orderRepository = $orderRepository;
        $this->slotManager = $slotManager;
    }

    public function removeIdleSlots(string $expirationPeriod): void
    {
        $expiredSlotCarts = $this->orderRepository->findCartsNotModifiedSince(new DateTime('-' . $expirationPeriod));

        /** @var OrderInterface $expiredSlotCart */
        foreach ($expiredSlotCarts as $expiredSlotCart) {
            foreach ($expiredSlotCart->getSlots() as $slot) {
                $this->slotManager->remove($slot);
            }
        }

        $this->slotManager->flush();
    }

    /**
     * @return void
     */
    public function removeOrderSlots(OrderInterface $order): void
    {
        foreach ($order->getSlots() as $slot) {
            $this->slotManager->remove($slot);
        }
        $this->slotManager->flush();
    }
}
