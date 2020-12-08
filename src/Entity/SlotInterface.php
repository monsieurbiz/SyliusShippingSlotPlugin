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
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface SlotInterface extends ResourceInterface, TimestampableInterface
{
    /**
     * Get slot ID.
     *
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * Set slot ID.
     *
     * @param int $id
     */
    public function setId(int $id): void;

    /**
     * Get slot timestamp.
     *
     * @return DateTimeInterface|null
     */
    public function getTimestamp(): ?DateTimeInterface;

    /**
     * Set slot timestamp.
     *
     * @param DateTimeInterface $timestamp
     */
    public function setTimestamp(DateTimeInterface $timestamp): void;

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
     * Get the shipment of the slot.
     *
     * @return ShipmentInterface|null
     */
    public function getShipment(): ?ShipmentInterface;

    /**
     * Set the shipment of the slot.
     *
     * @param ShipmentInterface $shipment
     */
    public function setShipment(ShipmentInterface $shipment): void;
}
