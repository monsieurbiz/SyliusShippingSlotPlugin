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

namespace MonsieurBiz\SyliusShippingSlotPlugin\Entity;

trait OrderTrait
{
    /**
     * {@inheritDoc}
     */
    public function getSlots(): array
    {
        if (!$this->hasShipments()) {
            return [];
        }

        $slots = [];
        foreach ($this->getShipments() as $shipment) {
            if (null !== ($slot = $shipment->getSlot())) {
                $slots[] = $slot;
            }
        }

        return $slots;
    }
}
