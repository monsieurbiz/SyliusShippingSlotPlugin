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

use DateInterval;
use DateTimeInterface;
use Recurr\Recurrence;
use Recurr\Rule as Rrule;
use Recurr\Transformer\ArrayTransformer;
use Recurr\Transformer\Constraint\AfterConstraint;
use Recurr\Transformer\Constraint\BeforeConstraint;
use Recurr\Transformer\Constraint\BetweenConstraint;
use Recurr\Transformer\ConstraintInterface;

class ShippingSlotConfig implements ShippingSlotConfigInterface
{
    private ?int $id = null;
    private ?string $name = null;
    private ?string $timezone = null;
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
    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    /**
     * {@inheritDoc}
     */
    public function setTimezone(?string $timezone): void
    {
        $this->timezone = $timezone;
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

    /**
     * {@inheritDoc}
     */
    public function getSlotDelay(): int
    {
        return
            (int) $this->getPreparationDelay() > (int) $this->getPickupDelay() ?
            (int) $this->getPreparationDelay() : (int) $this->getPickupDelay();
    }

    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getRecurrences(
        ?DateTimeInterface $startDate = null,
        ?DateTimeInterface $endDate = null
    ): array {
        $recurrences = [];

        switch (true) {
            case null !== $startDate && null !== $endDate:
                $constraint = new BetweenConstraint($startDate, $endDate);
                break;
            case null !== $startDate:
                $constraint = new AfterConstraint($startDate);
                break;
            case null !== $endDate:
                $constraint = new BeforeConstraint($endDate);
                break;
            default:
                $constraint = null;
        }

        foreach ($this->getRrules() ?? [] as $rrule) {
            $recurrences = array_merge($recurrences, $this->rruleToRecurrences($rrule, $constraint));
        }

        return $recurrences;
    }

    /**
     * @param string $rrule
     * @param ConstraintInterface|null $constraint
     *
     * @return Recurrence[]
     */
    private function rruleToRecurrences(string $rrule, ?ConstraintInterface $constraint): array
    {
        // Transform Rrule in a list of recurrences
        return (new ArrayTransformer())->transform(new Rrule($rrule), $constraint)->map(function(Recurrence $recurrence) {
            // Update end date with the slot duration on each recureence
            $recurrence->setEnd($recurrence->getEnd()->add(new DateInterval(sprintf('PT%dM', $this->getDurationRange()))));

            return $recurrence;
        })->toArray();
    }

    public function __toString(): string
    {
        return (string) $this->getName();
    }
}
