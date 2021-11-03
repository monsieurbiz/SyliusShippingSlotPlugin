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

namespace MonsieurBiz\SyliusShippingSlotPlugin\Generator;

use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ProductVariantInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShipmentInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShippingMethodInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\SlotInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Event\RecurrenceGenerationEvent;
use MonsieurBiz\SyliusShippingSlotPlugin\Repository\SlotRepositoryInterface;
use Recurr\Recurrence;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SlotGenerator implements SlotGeneratorInterface
{
    private CartContextInterface $cartContext;
    private FactoryInterface $slotFactory;
    private ShippingMethodRepositoryInterface $shippingMethodRepository;
    private SlotRepositoryInterface $slotRepository;
    private EntityManagerInterface $slotManager;
    private EntityManagerInterface $shipmentManager;
    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(
        CartContextInterface $cartContext,
        FactoryInterface $slotFactory,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        SlotRepositoryInterface $slotRepository,
        EntityManagerInterface $slotManager,
        EntityManagerInterface $shipmentManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->cartContext = $cartContext;
        $this->slotFactory = $slotFactory;
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->slotRepository = $slotRepository;
        $this->slotManager = $slotManager;
        $this->shipmentManager = $shipmentManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function createFromCheckout(
        string $shippingMethod,
        int $shipmentIndex,
        DateTimeInterface $startDate
    ): SlotInterface {
        /** @var OrderInterface $order */
        $order = $this->cartContext->getCart();
        $shipments = $order->getShipments();

        /** @var ShipmentInterface|null $shipment */
        $shipment = $shipments->get($shipmentIndex) ?? null;
        if (null === $shipment) {
            throw new Exception(sprintf('Cannot find shipment index "%d"', $shipmentIndex));
        }

        /** @var ShippingMethodInterface|null $shippingMethod */
        $shippingMethod = $this->shippingMethodRepository->findOneBy(['code' => $shippingMethod]);
        if (null === $shippingMethod) {
            throw new Exception(sprintf('Cannot find shipping method "%s"', $shippingMethod));
        }

        $shippingSlotConfig = $shippingMethod->getShippingSlotConfig();
        if (null === $shippingSlotConfig) {
            throw new Exception(sprintf('Cannot find slot configuration for shipping method "%s"', $shippingMethod->getName()));
        }

        $slot = $shipment->getSlot();
        if (null === $slot) {
            $slot = $this->slotFactory->createNew();
        }
        /** @var SlotInterface $slot */
        $slot->setShipment($shipment);
        /** @var DateTime $startDate */
        $slot->setTimestamp($startDate->setTimezone(new DateTimeZone('UTC')));
        $slot->setDurationRange($shippingSlotConfig->getDurationRange());
        $slot->setPickupDelay($shippingSlotConfig->getPickupDelay());
        $slot->setPreparationDelay($shippingSlotConfig->getPreparationDelay());

        $this->slotManager->persist($slot);
        $this->slotManager->flush();

        $shipment->setMethod($shippingMethod);

        $this->shipmentManager->persist($slot);
        $this->shipmentManager->flush();

        return $slot;
    }

    public function resetSlot(int $shipmentIndex): void
    {
        /** @var OrderInterface $order */
        $order = $this->cartContext->getCart();
        $shipments = $order->getShipments();

        /** @var ShipmentInterface|null $shipment */
        $shipment = $shipments->get($shipmentIndex) ?? null;
        if (null === $shipment) {
            throw new Exception(sprintf('Cannot find shipment index "%d"', $shipmentIndex));
        }

        /** @var SlotInterface|null $slot */
        $slot = $shipment->getSlot();
        if (null === $slot) {
            return;
        }

        $this->slotManager->remove($slot);
        $this->slotManager->flush();
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getSlotByMethod(ShippingMethodInterface $shippingMethod): ?SlotInterface
    {
        /** @var OrderInterface $order */
        $order = $this->cartContext->getCart();
        $shipments = $order->getShipments();

        /** @var ShipmentInterface $shipment */
        foreach ($shipments as $shipment) {
            if (null !== $shipment && null !== $shipment->getMethod() && $shipment->getMethod()->getCode() === $shippingMethod->getCode()) {
                return $shipment->getSlot();
            }
        }

        return null;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getFullSlots(ShippingMethodInterface $shippingMethod, ?DateTimeInterface $from): array
    {
        if (null === ($shippingSlotConfig = $shippingMethod->getShippingSlotConfig())) {
            return [];
        }

        $fullTimestamps = [];
        $slotsByTimestamp = [];
        $availableSpots = (int) $shippingSlotConfig->getAvailableSpots();
        $currentSlot = $this->getSlotByMethod($shippingMethod);
        $slots = $this->slotRepository->findByMethodAndStartDate($shippingMethod, $from);
        /** @var SlotInterface $slot */
        foreach ($slots as $slot) {
            if ($currentSlot === $slot) {
                continue;
            }
            /** @var DateTime $timestamp */
            $timestamp = $slot->getTimestamp();
            $slotsByTimestamp[$timestamp->format(DateTime::W3C)][] = $slot;
        }

        $fullTimestamps = array_filter($slotsByTimestamp, function($timestampSlots) use ($availableSpots) {
            return \count($timestampSlots) >= $availableSpots;
        });

        $fullSlots = [];
        foreach ($fullTimestamps as $slots) {
            $fullSlots = array_merge($fullSlots, $slots);
        }

        return $fullSlots;
    }

    public function isFull(SlotInterface $slot): bool
    {
        $shipment = $slot->getShipment();
        if (null === $shipment) {
            return false;
        }

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $shipment->getMethod();
        if (null === ($shippingSlotConfig = $shippingMethod->getShippingSlotConfig())) {
            return false;
        }

        $slots = $this->slotRepository->findByMethodAndDate($shippingMethod, $slot->getTimestamp());

        return \count($slots) > (int) $shippingSlotConfig->getAvailableSpots(); // Not >= because we have the current user slot
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function generateCalendarEvents(
        ShippingMethodInterface $shippingMethod,
        DateTimeInterface $startDate,
        DateTimeInterface $endDate
    ): array {
        $shippingSlotConfig = $shippingMethod->getShippingSlotConfig();
        if (null === $shippingSlotConfig) {
            return [];
        }
        $recurrences = $shippingSlotConfig->getRecurrences($startDate, $endDate, $this->getCartPreparationDelay());
        $event = new RecurrenceGenerationEvent($recurrences);
        $this->eventDispatcher->dispatch($event);
        $recurrences = $event->getRecurrences();

        $currentSlot = $this->getSlotByMethod($shippingMethod);
        $fullSlots = $this->getFullSlots($shippingMethod, $startDate);
        $unavailableTimestamps = array_map(function(SlotInterface $slot) {
            return $slot->getTimestamp();
        }, $fullSlots);

        $events = [];
        foreach ($recurrences as $recurrence) {
            if ($this->isUnaivalableRecurrence($recurrence, $unavailableTimestamps)) {
                continue;
            }
            $events[] = [
                'start' => $recurrence->getStart()->format(DateTime::W3C),
                'end' => $recurrence->getEnd()->format(DateTime::W3C),
                'extendedProps' => [
                    'isCurrent' => null !== $currentSlot && $currentSlot->getTimestamp() == $recurrence->getStart(),
                ],
            ];
        }

        return $events;
    }

    private function isUnaivalableRecurrence(Recurrence $recurrence, array $unavailableTimestamps): bool
    {
        foreach ($unavailableTimestamps as $unavailableTimestamp) {
            if ($unavailableTimestamp == $recurrence->getStart()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function getCartPreparationDelay(): ?int
    {
        $maxPreparationDelay = null;
        $cartItems = $this->cartContext->getCart()->getItems();

        /** @var OrderItemInterface $cartItem */
        foreach ($cartItems as $cartItem) {
            if (
                null !== ($variant = $cartItem->getVariant())
                && $variant instanceof ProductVariantInterface
                && null !== ($preparationDelay = $variant->getPreparationDelay())
                && (null === $maxPreparationDelay || $preparationDelay > $maxPreparationDelay)
            ) {
                $maxPreparationDelay = $preparationDelay;
            }
        }

        return $maxPreparationDelay;
    }
}
