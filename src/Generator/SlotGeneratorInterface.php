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

use MonsieurBiz\SyliusShippingSlotPlugin\Entity\SlotInterface;
use DateTimeInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShippingMethodInterface;

interface SlotGeneratorInterface
{
    /**
     * @return SlotInterface
     */
    public function createFromCheckout(string $shippingMethod, int $shipmentIndex, DateTimeInterface $startDate): SlotInterface;

    /**
     * @return void
     */
    public function resetSlot(int $shipmentIndex): void;

    /**
     * @return SlotInterface|null
     */
    public function getSlot(int $shipmentIndex): ?SlotInterface;

    /**
     * @return array
     */
    public function getUnavailableTimestamps(ShippingMethodInterface $shippingMethod, ?DateTimeInterface $from): array;

    /**
     * @return bool
     */
    public function isFull(SlotInterface $slot): bool;
}
