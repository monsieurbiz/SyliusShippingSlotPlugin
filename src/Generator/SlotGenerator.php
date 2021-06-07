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
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShipmentInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShippingMethodInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\SlotInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Repository\SlotRepositoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

class SlotGenerator implements SlotGeneratorInterface
{
    private CartContextInterface $cartContext;
    private FactoryInterface $slotFactory;
    private ShippingMethodRepositoryInterface $shippingMethodRepository;
    private SlotRepositoryInterface $slotRepository;
    private EntityManagerInterface $slotManager;

    /**
     * @param CartContextInterface $cartContext
     * @param FactoryInterface $slotFactory
     * @param ShippingMethodRepositoryInterface $shippingMethodRepository
     * @param SlotRepositoryInterface $slotRepository
     * @param EntityManagerInterface $slotManager
     */
    public function __construct(
        CartContextInterface $cartContext,
        FactoryInterface $slotFactory,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        SlotRepositoryInterface $slotRepository,
        EntityManagerInterface $slotManager
    ) {
        $this->cartContext = $cartContext;
        $this->slotFactory = $slotFactory;
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->slotRepository = $slotRepository;
        $this->slotManager = $slotManager;
    }

    /**
     * @return SlotInterface
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

        return $slot;
    }

    /**
     * @return void
     */
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
     * @return SlotInterface|null
     */
    public function getSlot(int $shipmentIndex): ?SlotInterface
    {
        /** @var OrderInterface $order */
        $order = $this->cartContext->getCart();
        $shipments = $order->getShipments();

        /** @var ShipmentInterface|null $shipment */
        $shipment = $shipments->get($shipmentIndex) ?? null;
        if (null === $shipment) {
            throw new Exception(sprintf('Cannot find shipment index "%d"', $shipmentIndex));
        }

        return $shipment->getSlot();
    }

    public function getUnavailableTimestamps(ShippingMethodInterface $shippingMethod, ?DateTimeInterface $from): array
    {
        if (null === ($shippingSlotConfig = $shippingMethod->getShippingSlotConfig())) {
            return [];
        }

        $fullTimestamps = [];
        $slotsByTimestamp = [];
        $availableSpots = (int) $shippingSlotConfig->getAvailableSpots();

        $slots = $this->slotRepository->findByMethodAndStartDate($shippingMethod, $from);
        /** @var SlotInterface $slot */
        foreach ($slots as $slot) {
            /** @var DateTime $timestamp */
            $timestamp = $slot->getTimestamp();
            $slotsByTimestamp[$timestamp->format(DateTime::W3C)][] = $slot;
        }

        // Add full slots in unavailable list
        foreach ($slotsByTimestamp as $timestamp => $timestampSlots) {
            if (\count($timestampSlots) >= $availableSpots) {
                $fullTimestamps[] = $timestamp;
            }
        }

        return $fullTimestamps;
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
}
