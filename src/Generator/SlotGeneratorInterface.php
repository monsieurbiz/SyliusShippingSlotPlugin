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

namespace MonsieurBiz\SyliusShippingSlotPlugin\Generator;

use DateTimeInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShippingMethodInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\SlotInterface;

interface SlotGeneratorInterface
{
    public function createFromCheckout(string $shippingMethod, int $shipmentIndex, DateTimeInterface $startDate): SlotInterface;

    public function resetSlot(int $shipmentIndex): void;

    public function getSlotByMethod(ShippingMethodInterface $shippingMethod): ?SlotInterface;

    public function getFullSlots(ShippingMethodInterface $shippingMethod, ?DateTimeInterface $from): array;

    public function isFull(SlotInterface $slot): bool;

    public function generateCalendarEvents(
        ShippingMethodInterface $shippingMethod,
        DateTimeInterface $startDate,
        DateTimeInterface $endDate
    ): array;
}
