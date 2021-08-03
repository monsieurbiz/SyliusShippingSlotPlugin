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
     * @param string $code
     *
     * @return Response
     */
    public function initAction(string $code): Response
    {
        // Find shipping method from code
        /** @var ShippingMethodInterface|null $shippingMethod */
        $shippingMethod = $this->shippingMethodRepository->findOneBy(['code' => $code]);
        if (null === $shippingMethod) {
            throw $this->createNotFoundException(sprintf('Shipping method "%s" not found', $code));
        }

        // No need to load calendar if shipping method has no slot configuration
        if (!($shipingSlotConfig = $shippingMethod->getShippingSlotConfig())) {
            return new JsonResponse(['code' => $code]);
        }

        return new JsonResponse([
            'code' => $code,
            'events' => [], // Events are loaded dynamically when full calendar ask it
            'timezone' => $shipingSlotConfig->getTimezone() ?? 'UTC',
        ]);
    }

    /**
     * @param string $code
     * @param string $fromDate
     * @param string $toDate
     *
     * @return Response
     */
    public function listAction(string $code, string $fromDate, string $toDate): Response
    {
        // Find shipping method from code
        /** @var ShippingMethodInterface|null $shippingMethod */
        $shippingMethod = $this->shippingMethodRepository->findOneBy(['code' => $code]);
        if (null === $shippingMethod) {
            throw $this->createNotFoundException(sprintf('Shipping method "%s" not found', $code));
        }

        // Shipping method not compatible with shipping slots
        if (!($shipingSlotConfig = $shippingMethod->getShippingSlotConfig())) {
            throw $this->createNotFoundException(sprintf('Shipping method "%s" is not compatible with shipping slots', $code));
        }

        $startDate = new DateTime($fromDate);
        $endDate = new DateTime($toDate);

        $recurrences = $shipingSlotConfig->getRecurrences($startDate, $endDate);
        $events = [];
        foreach ($recurrences as $recurrence) {
            $events[] = [
                'start' => $recurrence->getStart()->format(DateTime::W3C),
                'end' => $recurrence->getEnd()->format(DateTime::W3C),
            ];
        }

        return new JsonResponse($events);
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
        if (!($startDate = $slotElement['event']['start'] ?? false)) {
            throw $this->createNotFoundException('Start date not defined');
        }

        try {
            $this->slotGenerator->createFromCheckout(
                $shippingMethod,
                (int) $shipmentIndex,
                new DateTime($startDate)
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
     * @param int $shipmentIndex
     *
     * @return Response
     */
    public function getAction(int $shipmentIndex): Response
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
