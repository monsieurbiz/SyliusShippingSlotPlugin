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

use DateTimeInterface;
use Recurr\Recurrence;
use Sylius\Component\Resource\Model\ResourceInterface;

interface ShippingSlotConfigInterface extends ResourceInterface
{
    /**
     * Get shipping config name.
     *
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Set shipping config name.
     *
     * @param string|null $name
     */
    public function setName(?string $name): void;

    /**
     * Get shipping config timezone.
     *
     * @return string|null
     */
    public function getTimezone(): ?string;

    /**
     * Set shipping config timezone.
     *
     * @param string|null $timezone
     */
    public function setTimezone(?string $timezone): void;

    /**
     * Get the list of RRULES for this config.
     *
     * @return string[]|null
     */
    public function getRrules(): ?array;

    /**
     * Set the list of RRULES for this config.
     *
     * @param string[]|null $rrules
     */
    public function setRrules(?array $rrules): void;

    /**
     * Get the preparation delay in minutes.
     *
     * @return int|null
     */
    public function getPreparationDelay(): ?int;

    /**
     * Set the preparation delay in minutes.
     *
     * @param int|null $preparationDelay
     */
    public function setPreparationDelay(?int $preparationDelay): void;

    /**
     * Get the pickup delay in minutes.
     *
     * @return int|null
     */
    public function getPickupDelay(): ?int;

    /**
     * Set the pickup delay in minutes.
     *
     * @param int|null $pickupDelay
     */
    public function setPickupDelay(?int $pickupDelay): void;

    /**
     * Get the duration range of the slot.
     *
     * @return int|null
     */
    public function getDurationRange(): ?int;

    /**
     * Set the duration range of the slot.
     *
     * @param int|null $durationRange
     */
    public function setDurationRange(?int $durationRange): void;

    /**
     * Get the number of available spots for a slot.
     *
     * @return int|null
     */
    public function getAvailableSpots(): ?int;

    /**
     * Set the number of available spots for a slot.
     *
     * @param int|null $availableSpots
     */
    public function setAvailableSpots(?int $availableSpots): void;

    /**
     * Get the color displayed in calendar.
     *
     * @return string|null
     */
    public function getColor(): ?string;

    /**
     * Set the color displayed in calendar.
     *
     * @param string|null $color
     */
    public function setColor(?string $color): void;

    /**
     * Get the delay to add to fetch the first slot.
     *
     * @return int
     */
    public function getSlotDelay(): int;

    /**
     * Get available recurrences from Rrules for given dates.
     *
     * @param DateTimeInterface|null $startDate
     * @param DateTimeInterface|null $endDate
     *
     * @return Recurrence[]
     */
    public function getRecurrences(
        ?DateTimeInterface $startDate = null,
        ?DateTimeInterface $endDate = null
    ): array;
}
