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

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShippingMethodInterface as MonsieurBizShippingMethodInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShippingMethodTrait;
use Sylius\Component\Core\Model\ShippingMethod as SyliusShippingMethod;
use Sylius\Component\Core\Model\ShippingMethodInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_shipping_method")
 */
class ShippingMethod extends SyliusShippingMethod implements ShippingMethodInterface, MonsieurBizShippingMethodInterface
{
    use ShippingMethodTrait;
}
