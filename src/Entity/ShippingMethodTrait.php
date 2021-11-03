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

use Doctrine\ORM\Mapping as ORM;

trait ShippingMethodTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="\MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShippingSlotConfigInterface")
     * @ORM\JoinColumn(name="shipping_slot_config_id", nullable=true, referencedColumnName="id", onDelete="SET NULL")
     */
    private ?ShippingSlotConfigInterface $shippingSlotConfig = null;

    public function getShippingSlotConfig(): ?ShippingSlotConfigInterface
    {
        return $this->shippingSlotConfig;
    }

    public function setShippingSlotConfig(?ShippingSlotConfigInterface $shippingSlotConfig): void
    {
        $this->shippingSlotConfig = $shippingSlotConfig;
    }
}
