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

namespace MonsieurBiz\SyliusShippingSlotPlugin\Controller;

use DateInterval;
use DateTime;
use Exception;
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShippingMethodInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Generator\SlotGeneratorInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SlotController extends AbstractController
{
    private ShippingMethodRepositoryInterface $shippingMethodRepository;
    private SlotGeneratorInterface $slotGenerator;

    /**
     * @param ShippingMethodRepositoryInterface $shippingMethodRepository
     * @param SlotGeneratorInterface $slotGenerator
     */
    public function __construct(
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        SlotGeneratorInterface $slotGenerator
    ) {
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->slotGenerator = $slotGenerator;
    }

    /**
     * @param Request $request
     * @param string $code
     *
     * @return Response
     */
    public function listAction(Request $request, string $code): Response
    {
        // Find shipping method from code
        /** @var ShippingMethodInterface|null $shippingMethod */
        $shippingMethod = $this->shippingMethodRepository->findOneBy(['code' => $code]);
        if (null === $shippingMethod) {
            throw $this->createNotFoundException(sprintf('Shipping method "%s" not found', $code));
        }

        // No need to load slots if shipping method has no slot configuration
        if (!($shipingSlotConfig = $shippingMethod->getShippingSlotConfig())) {
            return new JsonResponse(['code' => $code]);
        }

        $startDate = new DateTime();
        $startDate->add(new DateInterval(sprintf('PT%dM', $shipingSlotConfig->getSlotDelay()))); // Add minutes delay

        return new JsonResponse([
            'code' => $code,
            'rrules' => $shipingSlotConfig->getRrules(),
            'duration' => $shipingSlotConfig->getDurationRange(),
            'startDate' => $startDate->format(DateTime::W3C),
            'unavailableDates' => $this->slotGenerator->getUnavailableTimestamps($shippingMethod, $startDate),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function saveAction(Request $request): Response
    {
        if (!($shippingMethod = $request->get('shippingMethod'))) {
            throw $this->createNotFoundException('Shipping method not defined');
        }

        if (null === ($shipmentIndex = $request->get('shipmentIndex'))) {
            throw $this->createNotFoundException('Shipment index not defined');
        }

        $slotElement = json_decode($request->get('slot', '{}'), true);
        if (!isset($slotElement['event']) || !isset($slotElement['event']['start'])) {
            throw $this->createNotFoundException('Start date not defined');
        }

        try {
            $slot = $this->slotGenerator->createFromCheckout(
                $shippingMethod,
                (int) $shipmentIndex,
                new DateTime($slotElement['event']['start'])
            );
        } catch (Exception $e) {
            throw $this->createNotFoundException($e->getMessage());
        }

        return new JsonResponse($slotElement);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function resetAction(Request $request): Response
    {
        if (null === ($shipmentIndex = $request->get('shipmentIndex'))) {
            throw $this->createNotFoundException('Shipment index not defined');
        }

        try {
            $this->slotGenerator->resetSlot((int) $shipmentIndex);
        } catch (Exception $e) {
            throw $this->createNotFoundException($e->getMessage());
        }

        return new JsonResponse([]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function getAction(Request $request, int $shipmentIndex): Response
    {
        try {
            $slot = $this->slotGenerator->getSlot($shipmentIndex);
        } catch (Exception $e) {
            throw $this->createNotFoundException($e->getMessage());
        }

        if (null === $slot) {
            return new JsonResponse([]);
        }

        /** @var DateTime $timestamp */
        $timestamp = $slot->getTimestamp();

        return new JsonResponse([
            'duration' => $slot->getDurationRange(),
            'startDate' => $timestamp->format(DateTime::W3C),
        ]);
    }
}
