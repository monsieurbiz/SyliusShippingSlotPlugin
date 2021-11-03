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

namespace MonsieurBiz\SyliusShippingSlotPlugin\Message;

class ShipmentMessage
{
    private int $shipmentId;

    public function __construct(int $shipmentId)
    {
        $this->shipmentId = $shipmentId;
    }

    public function getShipmentId(): int
    {
        return $this->shipmentId;
    }
}
