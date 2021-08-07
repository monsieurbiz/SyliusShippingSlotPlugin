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

namespace MonsieurBiz\SyliusShippingSlotPlugin\Transition;

use MonsieurBiz\SyliusShippingSlotPlugin\Entity\OrderInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Remover\SlotRemoverInterface;

final class OrderCancelTransition
{
    private SlotRemoverInterface $slotRemover;

    public function __construct(SlotRemoverInterface $slotRemover)
    {
        $this->slotRemover = $slotRemover;
    }

    public function __invoke(OrderInterface $order): void
    {
        $this->slotRemover->removeOrderSlots($order);
    }
}
