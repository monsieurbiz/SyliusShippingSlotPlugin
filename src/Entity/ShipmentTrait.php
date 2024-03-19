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

trait ShipmentTrait
{
    /**
     * @ORM\OneToOne(targetEntity="MonsieurBiz\SyliusShippingSlotPlugin\Entity\SlotInterface", mappedBy="shipment", fetch="EAGER")
     */
    private ?SlotInterface $slot = null;

    public function getSlot(): ?SlotInterface
    {
        return $this->slot;
    }

    public function setSlot(?SlotInterface $slot): void
    {
        $this->slot = $slot;
    }
}
