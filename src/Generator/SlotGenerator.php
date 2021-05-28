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

use MonsieurBiz\SyliusShippingSlotPlugin\Entity\SlotInterface;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShipmentInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShippingMethodInterface;

class SlotGenerator implements SlotGeneratorInterface
{
    private CartContextInterface $cartContext;
    private FactoryInterface $slotFactory;
    private RepositoryInterface $shippingMethodRepository;
    private EntityManagerInterface $slotManager;

    /**
     * @param CartContextInterface $cartContext
     * @param FactoryInterface $slotFactory
     * @param RepositoryInterface $shippingMethodRepository
     * @param EntityManagerInterface $slotManager
     */
    public function __construct(
        CartContextInterface $cartContext,
        FactoryInterface $slotFactory,
        RepositoryInterface $shippingMethodRepository,
        EntityManagerInterface $slotManager
    ) {
        $this->cartContext = $cartContext;
        $this->slotFactory = $slotFactory;
        $this->shippingMethodRepository = $shippingMethodRepository;
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

        /** @var ShipmentInterface $shipment */
        $shipment = $shipments->get($shipmentIndex) ?? null;
        if (null === $shipment) {
            throw new Exception(sprintf('Cannot find shipment index "%d"', $shipmentIndex));
        }

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $this->shippingMethodRepository->findOneBy(['code' => $shippingMethod]);
        if (null === $shippingMethod) {
            throw new Exception(sprintf('Cannot find shipping method "%s"', $shippingMethod));
        }

        $shippingSlotConfig = $shippingMethod ->getShippingSlotConfig();
        if (null === $shippingSlotConfig) {
            throw new Exception(sprintf('Cannot find slot configuration for shipping method "%s"', $shippingMethod));
        }

        /** @var SlotInterface $slot */
        if (null === ($slot = $shipment->getSlot())) {
            $slot = $this->slotFactory->createNew();
        }
        $slot->setShipment($shipment);
        $slot->setTimestamp($startDate);
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

        /** @var ShipmentInterface $shipment */
        $shipment = $shipments->get($shipmentIndex) ?? null;
        if (null === $shipment) {
            throw new Exception(sprintf('Cannot find shipment index "%d"', $shipmentIndex));
        }

        /** @var SlotInterface $slot */
        if (null === ($slot = $shipment->getSlot())) {
            return;
        }

        $this->slotManager->remove($slot);
        $this->slotManager->flush();
    }
}
