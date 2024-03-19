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

namespace MonsieurBiz\SyliusShippingSlotPlugin\Resolver;

use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShippingSlotConfigInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;

interface ShippingSlotConfigResolverInterface
{
    public function getShippingSlotConfig(?ShipmentInterface $shipment, ShippingMethodInterface $shippingMethod): ?ShippingSlotConfigInterface;
}
