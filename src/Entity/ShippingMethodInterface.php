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

interface ShippingMethodInterface
{
    /**
     * Get the shipping slot config.
     *
     * @return ShippingSlotConfigInterface|null
     */
    public function getShippingSlotConfig(): ?ShippingSlotConfigInterface;

    /**
     * Set the shipping slot config.
     *
     * @param ShippingSlotConfigInterface $shippingSlotConfig
     *
     * @return void
     */
    public function setShippingSlotConfig(ShippingSlotConfigInterface $shippingSlotConfig): void;
}
