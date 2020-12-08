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

use Sylius\Component\Resource\Model\ResourceInterface;

interface ShippingSlotConfigInterface extends ResourceInterface
{
    /**
     * Get shipping config ID.
     *
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * Set shipping config ID.
     *
     * @param int $id
     */
    public function setId(int $id): void;

    /**
     * Get shipping config name.
     *
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Set shipping config name.
     *
     * @param string $name
     */
    public function setName(string $name): void;

    /**
     * Get the list of RRULES for this config.
     *
     * @return string[]|null
     */
    public function getRrules(): ?array;

    /**
     * Set the list of RRULES for this config.
     *
     * @param string[] $rrules
     */
    public function setRrules(array $rrules): void;

    /**
     * Get the preparation delay in minutes.
     *
     * @return int|null
     */
    public function getPreparationDelay(): ?int;

    /**
     * Set the preparation delay in minutes.
     *
     * @param int $preparationDelay
     */
    public function setPreparationDelay(int $preparationDelay): void;

    /**
     * Get the pickup delay in minutes.
     *
     * @return int|null
     */
    public function getPickupDelay(): ?int;

    /**
     * Set the pickup delay in minutes.
     *
     * @param int $pickupDelay
     */
    public function setPickupDelay(int $pickupDelay): void;

    /**
     * Get the duration range of the slot.
     *
     * @return int|null
     */
    public function getDurationRange(): ?int;

    /**
     * Set the duration range of the slot.
     *
     * @param int $durationRange
     */
    public function setDurationRange(int $durationRange): void;

    /**
     * Get the number of available spots for a slot.
     *
     * @return int|null
     */
    public function getAvailableSpots(): ?int;

    /**
     * Set the number of available spots for a slot.
     *
     * @param int $availableSpots
     */
    public function setAvailableSpots(int $availableSpots): void;

    /**
     * Get the color displayed in calendar.
     *
     * @return string|null
     */
    public function getColor(): ?string;

    /**
     * Set the color displayed in calendar.
     *
     * @param string $color
     */
    public function setColor(string $color): void;
}
