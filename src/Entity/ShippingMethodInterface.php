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

namespace MonsieurBiz\SyliusShippingSlotPlugin\Entity;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ShippingMethodInterface as SyliusShippingMethodInterface;

interface ShippingMethodInterface extends SyliusShippingMethodInterface
{
    /**
     * @deprecated Use getShippingSlotConfigs instead
     */
    public function getShippingSlotConfig(): ?ShippingSlotConfigInterface;

    /**
     * @deprecated Use setShippingSlotConfigs instead
     */
    public function setShippingSlotConfig(ShippingSlotConfigInterface $shippingSlotConfig): void;

    /**
     * @return Collection<array-key, ShippingSlotConfigInterface>
     */
    public function getShippingSlotConfigs(): Collection;

    /**
     * @param Collection<array-key, ShippingSlotConfigInterface> $shippingSlotConfigs
     */
    public function setShippingSlotConfigs(Collection $shippingSlotConfigs): void;
}
