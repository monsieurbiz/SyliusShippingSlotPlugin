<?php

/*
 * This file is part of SyliusShippingSlotPlugin website.
 *
 * (c) SyliusShippingSlotPlugin <sylius+syliusshippingslotplugin@monsieurbiz.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Entity\Shipping;

use Doctrine\ORM\Mapping as ORM;
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\Slot as BaseSlot;

/**
 * @ORM\Entity
 * @ORM\Table(name="monsieurbiz_shipping_slot_slot")
 */
final class Slot extends BaseSlot
{

}