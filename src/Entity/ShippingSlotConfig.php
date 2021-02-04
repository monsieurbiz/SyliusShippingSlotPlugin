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

class ShippingSlotConfig implements ShippingSlotConfigInterface
{
    private ?int $id = null;
    private ?string $name = null;
    private ?array $rrules = null;
    private ?int $preparationDelay = null;
    private ?int $pickupDelay = null;
    private ?int $durationRange = null;
    private ?int $availableSpots = null;
    private ?string $color = null;

    /**
     * {@inheritDoc}
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * {@inheritDoc}
     */
    public function getRrules(): ?array
    {
        return $this->rrules;
    }

    /**
     * {@inheritDoc}
     */
    public function setRrules(?array $rrules): void
    {
        $this->rrules = $rrules;
    }

    /**
     * {@inheritDoc}
     */
    public function getPreparationDelay(): ?int
    {
        return $this->preparationDelay;
    }

    /**
     * {@inheritDoc}
     */
    public function setPreparationDelay(?int $preparationDelay): void
    {
        $this->preparationDelay = $preparationDelay;
    }

    /**
     * {@inheritDoc}
     */
    public function getPickupDelay(): ?int
    {
        return $this->pickupDelay;
    }

    /**
     * {@inheritDoc}
     */
    public function setPickupDelay(?int $pickupDelay): void
    {
        $this->pickupDelay = $pickupDelay;
    }

    /**
     * {@inheritDoc}
     */
    public function getDurationRange(): ?int
    {
        return $this->durationRange;
    }

    /**
     * {@inheritDoc}
     */
    public function setDurationRange(?int $durationRange): void
    {
        $this->durationRange = $durationRange;
    }

    /**
     * {@inheritDoc}
     */
    public function getAvailableSpots(): ?int
    {
        return $this->availableSpots;
    }

    /**
     * {@inheritDoc}
     */
    public function setAvailableSpots(?int $availableSpots): void
    {
        $this->availableSpots = $availableSpots;
    }

    /**
     * {@inheritDoc}
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * {@inheritDoc}
     */
    public function setColor(?string $color): void
    {
        $this->color = $color;
    }
}
