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
    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getTimezone(): ?string;

    public function setTimezone(?string $timezone): void;

    public function getRrules(): ?array;

    public function setRrules(?array $rrules): void;

    public function getPreparationDelay(): ?int;

    public function setPreparationDelay(?int $preparationDelay): void;

    public function getPickupDelay(): ?int;

    public function setPickupDelay(?int $pickupDelay): void;

    public function getDurationRange(): ?int;

    public function setDurationRange(?int $durationRange): void;

    public function getAvailableSpots(): ?int;

    public function setAvailableSpots(?int $availableSpots): void;

    public function getColor(): ?string;

    public function setColor(?string $color): void;

    public function getSlotDelay(): int;

    /**
     * @return Recurrence[]
     */
    public function getRecurrences(
        ?DateTimeInterface $startDate = null,
        ?DateTimeInterface $endDate = null,
        ?int $customPreparationDelay = null
    ): array;
}
