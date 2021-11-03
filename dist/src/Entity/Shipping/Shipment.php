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

namespace App\Entity\Shipping;

use Doctrine\ORM\Mapping as ORM;
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShipmentInterface as MonsieurBizShipmentInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShipmentTrait;
use Sylius\Component\Core\Model\Shipment as SyliusShipment;
use Sylius\Component\Core\Model\ShipmentInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_shipment")
 */
class Shipment extends SyliusShipment implements ShipmentInterface, MonsieurBizShipmentInterface
{
    use ShipmentTrait;
}
