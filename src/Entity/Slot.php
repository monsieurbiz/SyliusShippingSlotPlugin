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

use DateInterval;
use DateTime;
use DateTimeInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;

class Slot implements SlotInterface
{
    use TimestampableTrait;

    private ?int $id = null;

    private ?DateTimeInterface $timestamp = null;

    private ?int $preparationDelay = null;

    private ?int $pickupDelay = null;

    private ?int $durationRange = null;

    private ?ShipmentInterface $shipment = null;

    private ?ShippingSlotConfigInterface $shippingSlotConfig = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTimestamp(): ?DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(?DateTimeInterface $timetamp): void
    {
        $this->timestamp = $timetamp;
    }

    public function getPreparationDelay(): ?int
    {
        return $this->preparationDelay;
    }

    public function setPreparationDelay(?int $preparationDelay): void
    {
        $this->preparationDelay = $preparationDelay;
    }

    public function getPickupDelay(): ?int
    {
        return $this->pickupDelay;
    }

    public function setPickupDelay(?int $pickupDelay): void
    {
        $this->pickupDelay = $pickupDelay;
    }

    public function getDurationRange(): ?int
    {
        return $this->durationRange;
    }

    public function setDurationRange(?int $durationRange): void
    {
        $this->durationRange = $durationRange;
    }

    public function getShipment(): ?ShipmentInterface
    {
        return $this->shipment;
    }

    public function setShipment(?ShipmentInterface $shipment): void
    {
        $this->shipment = $shipment;
    }

    public function getSlotDelay(): int
    {
        return
            (int) $this->getPreparationDelay() > (int) $this->getPickupDelay() ?
            (int) $this->getPreparationDelay() : (int) $this->getPickupDelay();
    }

    public function isValid(): bool
    {
        $minDate = (new DateTime())->add(new DateInterval(sprintf('PT%dM', $this->getSlotDelay()))); // Add minutes delay

        // Too late the slot is not valid anymore
        if ($this->getTimestamp() < $minDate) {
            return false;
        }

        return true;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getTimezone(): string
    {
        $shippingSlotConfig = $this->getShippingSlotConfig();
        if (
            null !== $shippingSlotConfig
            && null !== ($timezone = $shippingSlotConfig->getTimezone())
        ) {
            return $timezone;
        }

        return 'UTC';
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getShippingSlotConfig(): ?ShippingSlotConfigInterface
    {
        if (null === $this->shippingSlotConfig) {
            if (
                null !== ($shipment = $this->getShipment())
                && null !== ($shippingMethod = $shipment->getMethod())
                && $shippingMethod instanceof ShippingMethodInterface
                && null !== ($shippingSlotConfig = $shippingMethod->getShippingSlotConfig())
            ) {
                $this->shippingSlotConfig = $shippingSlotConfig;
            }
        }

        return $this->shippingSlotConfig;
    }

    public function setShippingSlotConfig(?ShippingSlotConfigInterface $shippingSlotConfig): void
    {
        $this->shippingSlotConfig = $shippingSlotConfig;
    }
}
