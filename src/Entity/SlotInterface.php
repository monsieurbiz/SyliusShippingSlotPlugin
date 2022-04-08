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

use DateTimeInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface SlotInterface extends ResourceInterface, TimestampableInterface
{
    public function getId(): ?int;

    public function getTimestamp(): ?DateTimeInterface;

    public function setTimestamp(?DateTimeInterface $timestamp): void;

    public function getPreparationDelay(): ?int;

    public function setPreparationDelay(?int $preparationDelay): void;

    public function getPickupDelay(): ?int;

    public function setPickupDelay(?int $pickupDelay): void;

    public function getDurationRange(): ?int;

    public function setDurationRange(?int $durationRange): void;

    public function getShipment(): ?ShipmentInterface;

    public function setShipment(?ShipmentInterface $shipment): void;

    public function getSlotDelay(): int;

    public function isValid(): bool;

    public function getTimezone(): string;
}
