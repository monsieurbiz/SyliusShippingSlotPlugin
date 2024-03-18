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
use DateTimeZone;
use Recurr\Recurrence;
use Recurr\Rule as Rrule;
use Recurr\Transformer\ArrayTransformer;
use Recurr\Transformer\Constraint\AfterConstraint;
use Recurr\Transformer\Constraint\BeforeConstraint;
use Recurr\Transformer\Constraint\BetweenConstraint;
use Recurr\Transformer\ConstraintInterface;

class ShippingSlotConfig implements ShippingSlotConfigInterface
{
    protected ?int $id = null;

    protected ?string $name = null;

    protected ?string $timezone = null;

    protected ?array $rrules = null;

    protected ?int $preparationDelay = null;

    protected ?int $pickupDelay = null;

    protected ?int $durationRange = null;

    protected ?int $availableSpots = null;

    protected ?string $color = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(?string $timezone): void
    {
        $this->timezone = $timezone;
    }

    public function getRrules(): ?array
    {
        return $this->rrules;
    }

    public function setRrules(?array $rrules): void
    {
        $this->rrules = $rrules;
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

    public function getAvailableSpots(): ?int
    {
        return $this->availableSpots;
    }

    public function setAvailableSpots(?int $availableSpots): void
    {
        $this->availableSpots = $availableSpots;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): void
    {
        $this->color = $color;
    }

    public function getSlotDelay(): int
    {
        return
            (int) $this->getPreparationDelay() > (int) $this->getPickupDelay() ?
            (int) $this->getPreparationDelay() : (int) $this->getPickupDelay();
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getRecurrences(
        ?DateTimeInterface $startDate = null,
        ?DateTimeInterface $endDate = null,
        ?int $customPreparationDelay = null
    ): array {
        $recurrences = [];

        // Use greater preparation delay, for example a product can make some time to be produced
        $slotDelay = $this->getSlotDelay();
        if (null !== $customPreparationDelay && $customPreparationDelay > $slotDelay) {
            $slotDelay = $customPreparationDelay;
        }

        $minDate = (new DateTime())
            ->add(new DateInterval(sprintf('PT%dM', $slotDelay)))
            ->setTimezone(new DateTimeZone($this->getTimezone() ?? 'UTC'))
        ;

        if ($startDate < $minDate) {
            $startDate = $minDate;
        }

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

        foreach ($this->getRrules() ?? [] as $rruleString) {
            $rrule = new Rrule($rruleString);
            $rrule->setStartDate($startDate);
            $recurrences = array_merge($recurrences, $this->rruleToRecurrences($rrule, $constraint));
        }

        return $recurrences;
    }

    /**
     * @return Recurrence[]
     */
    private function rruleToRecurrences(Rrule $rrule, ?ConstraintInterface $constraint): array
    {
        // Transform Rrule in a list of recurrences
        return (new ArrayTransformer())
            ->transform($rrule, $constraint)
            ->map(function (Recurrence $recurrence) {
                // Update end date with the slot duration on each recurrence
                $recurrence->setEnd($recurrence->getEnd()->add(new DateInterval(sprintf('PT%dM', $this->getDurationRange()))));

                return $recurrence;
            })
            ->toArray()
        ;
    }

    public function __toString(): string
    {
        return (string) $this->getName();
    }
}
