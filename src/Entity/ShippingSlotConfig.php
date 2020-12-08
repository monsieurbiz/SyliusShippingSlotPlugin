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
    private ?int $id;
    private ?string $name;
    private ?array $rrules;
    private ?int $preparationDelay;
    private ?int $pickupDelay;
    private ?int $durationRange;
    private ?int $availableSpots;
    private ?string $color;

    /**
     * {@inheritDoc}
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getRrules(): ?array
    {
        return $this->rrules;
    }

    /**
     * {@inheritdoc}
     */
    public function setRrules(array $rrules): void
    {
        $this->rrules = $rrules;
    }

    /**
     * {@inheritdoc}
     */
    public function getPreparationDelay(): ?int
    {
        return $this->preparationDelay;
    }

    /**
     * {@inheritdoc}
     */
    public function setPreparationDelay(int $preparationDelay): void
    {
        $this->preparationDelay = $preparationDelay;
    }

    /**
     * {@inheritdoc}
     */
    public function getPickupDelay(): ?int
    {
        return $this->pickupDelay;
    }

    /**
     * {@inheritdoc}
     */
    public function setPickupDelay($pickupDelay): void
    {
        $this->pickupDelay = $pickupDelay;
    }

    /**
     * {@inheritdoc}
     */
    public function getDurationRange(): ?int
    {
        return $this->durationRange;
    }

    /**
     * {@inheritdoc}
     */
    public function setDurationRange(int $durationRange): void
    {
        $this->durationRange = $durationRange;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableSpots(): ?int
    {
        return $this->availableSpots;
    }

    /**
     * {@inheritdoc}
     */
    public function setAvailableSpots(int $availableSpots): void
    {
        $this->availableSpots = $availableSpots;
    }

    /**
     * {@inheritdoc}
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * {@inheritdoc}
     */
    public function setColor(string $color): void
    {
        $this->color = $color;
    }
}
