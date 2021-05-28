<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusShippingSlotPlugin\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShippingMethodInterface;
use DateTime;
use DateInterval;
use MonsieurBiz\SyliusShippingSlotPlugin\Generator\SlotGeneratorInterface;
use Exception;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class SlotController extends AbstractController
{
    private ShippingMethodRepositoryInterface $shippingMethodRepository;
    private RepositoryInterface $slotRepository;
    private SlotGeneratorInterface $slotGenerator;

    /**
     * @param ShippingMethodRepositoryInterface $shippingMethodRepository
     * @param SlotGeneratorInterface $slotGenerator
     */
    public function __construct(
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        RepositoryInterface $slotRepository,
        SlotGeneratorInterface $slotGenerator
    ) {
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->slotRepository = $slotRepository;
        $this->slotGenerator = $slotGenerator;
    }

    /**
     * @param Request $request
     * @param string $code
     * @return Response
     */
    public function listAction(Request $request, string $code): Response
    {
        // Find shipping method from code
        /** @var ShippingMethodInterface $shippingMethod */
        if (!$shippingMethod = $this->shippingMethodRepository->findOneBy(['code' => $code])) {
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

        return new JsonResponse([
            'duration' => $slot->getDurationRange(),
            'startDate' => $slot->getTimestamp()->format(DateTime::W3C),
        ]);
    }
}
