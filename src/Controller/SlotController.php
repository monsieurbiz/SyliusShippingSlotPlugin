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

namespace MonsieurBiz\SyliusShippingSlotPlugin\Controller;

use DateTime;
use Exception;
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShippingMethodInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShippingSlotConfigInterface;
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

    public function __construct(
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        SlotGeneratorInterface $slotGenerator
    ) {
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->slotGenerator = $slotGenerator;
    }

    public function initAction(string $code, ?int $shippingSlotConfig): Response
    {
        // Find shipping method from code
        /** @var ShippingMethodInterface|null $shippingMethod */
        $shippingMethod = $this->shippingMethodRepository->findOneBy(['code' => $code]);
        if (null === $shippingMethod) {
            throw $this->createNotFoundException(\sprintf('Shipping method "%s" not found', $code));
        }

        // No need to load calendar if shipping method has no slot configuration
        if (!($shippingSlotConfig = $this->getShippingSlotConfig($shippingMethod, $shippingSlotConfig))) {
            return new JsonResponse(['code' => $code]);
        }

        return new JsonResponse([
            'code' => $code,
            'events' => [], // Events are loaded dynamically when full calendar ask it
            'timezone' => $shippingSlotConfig->getTimezone() ?? 'UTC',
        ]);
    }

    public function listAction(string $code, string $fromDate, string $toDate, ?int $shippingSlotConfig): Response
    {
        // Find shipping method from code
        /** @var ShippingMethodInterface|null $shippingMethod */
        $shippingMethod = $this->shippingMethodRepository->findOneBy(['code' => $code]);
        if (null === $shippingMethod) {
            throw $this->createNotFoundException(\sprintf('Shipping method "%s" not found', $code));
        }

        // Shipping method not compatible with shipping slots
        if (null === ($shippingSlotConfig = $this->getShippingSlotConfig($shippingMethod, $shippingSlotConfig))) {
            throw $this->createNotFoundException(\sprintf('Shipping method "%s" is not compatible with shipping slots', $code));
        }

        return new JsonResponse($this->slotGenerator->generateCalendarEvents(
            $shippingMethod,
            new DateTime($fromDate),
            new DateTime($toDate),
            $shippingSlotConfig
        ));
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function saveAction(Request $request): Response
    {
        if (!($shippingMethod = $request->get('shippingMethod'))) {
            throw $this->createNotFoundException('Shipping method not defined');
        }

        if (null === ($shipmentIndex = $request->get('shipmentIndex'))) {
            throw $this->createNotFoundException('Shipment index not defined');
        }

        if (null === ($shippingSlotConfig = $request->get('shippingSlotConfig'))) {
            throw $this->createNotFoundException('Shipping slot config not defined');
        }

        $event = json_decode($request->get('event', '{}'), true);
        if (!($startDate = $event['start'] ?? false)) {
            throw $this->createNotFoundException('Start date not defined');
        }

        try {
            $this->slotGenerator->createFromCheckout(
                $shippingMethod,
                (int) $shipmentIndex,
                new DateTime($startDate),
                !empty($shippingSlotConfig) ? (int) $shippingSlotConfig : null
            );
        } catch (Exception $e) {
            throw $this->createNotFoundException($e->getMessage());
        }

        return new JsonResponse($event);
    }

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

    private function getShippingSlotConfig(ShippingMethodInterface $shippingMethod, ?int $shippingSlotConfig): ?ShippingSlotConfigInterface
    {
        $shippingSlotConfig = null !== $shippingSlotConfig ? $shippingMethod->getShippingSlotConfigs()->filter(
            fn (ShippingSlotConfigInterface $config) => $config->getId() === $shippingSlotConfig
        )->first() : $shippingMethod->getShippingSlotConfigs()->first();

        return $shippingSlotConfig ?: $shippingMethod->getShippingSlotConfig();
    }
}
