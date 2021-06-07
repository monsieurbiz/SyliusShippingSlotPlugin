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

namespace MonsieurBiz\SyliusShippingSlotPlugin;

interface MonsieurBizShippingSlotExpiredCartsEvents
{
    public const PRE_REMOVE = 'monsieurbiz_shipping_slot.cart_slots.pre_remove';

    public const POST_REMOVE = 'monsieurbiz_shipping_slot.cart_slots.post_remove';
}
