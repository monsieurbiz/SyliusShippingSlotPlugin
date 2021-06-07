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
use MonsieurBiz\SyliusShippingSlotPlugin\MonsieurBizShippingSlotExpiredCartsEvents;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class SlotRemover implements SlotRemoverInterface
{
    private OrderRepositoryInterface $orderRepository;
    private EntityManagerInterface $slotManager;
    private EventDispatcherInterface $eventDispatcher;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param EntityManagerInterface $slotManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        EntityManagerInterface $slotManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->orderRepository = $orderRepository;
        $this->slotManager = $slotManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function removeIdleSlots(string $expirationPeriod): void
    {
        $expiredSlotCarts = $this->orderRepository->findCartsNotModifiedSince(new DateTime('-' . $expirationPeriod));

        $this->eventDispatcher->dispatch(MonsieurBizShippingSlotExpiredCartsEvents::PRE_REMOVE, new GenericEvent($expiredSlotCarts));

        /** @var OrderInterface $expiredSlotCart */
        foreach ($expiredSlotCarts as $expiredSlotCart) {
            foreach ($expiredSlotCart->getSlots() as $slot) {
                $this->slotManager->remove($slot);
            }
        }

        $this->slotManager->flush();

        $this->eventDispatcher->dispatch(MonsieurBizShippingSlotExpiredCartsEvents::POST_REMOVE, new GenericEvent($expiredSlotCarts));
    }
}
