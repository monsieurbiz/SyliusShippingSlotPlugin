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

use Doctrine\ORM\Mapping as ORM;

trait ShipmentTrait
{
    /**
     * @ORM\OneToOne(targetEntity="MonsieurBiz\SyliusShippingSlotPlugin\Entity\Slot", mappedBy="shipment", fetch="EAGER")
     *
     * @var SlotInterface|null
     */
    private ?SlotInterface $slot = null;

    /**
     * {@inheritDoc}
     */
    public function getSlot(): ?SlotInterface
    {
        return $this->slot;
    }

    /**
     * {@inheritDoc}
     */
    public function setSlot(?SlotInterface $slot): void
    {
        $this->slot = $slot;
    }
}
