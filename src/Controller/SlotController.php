<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusShippingSlotPlugin\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;


class SlotController extends AbstractController
{
    private ShippingMethodRepositoryInterface $shippingMethodRepository;

    public function __construct(ShippingMethodRepositoryInterface $shippingMethodRepository)
    {
        $this->shippingMethodRepository = $shippingMethodRepository;
    }

    public function listAction(Request $request, string $code): Response
    {
        // Find shipping method from code
        if (!$shippingMethod = $this->shippingMethodRepository->findOneBy(['code' => $code])) {
            throw $this->createNotFoundException(sprintf('Shipping method "%s" not found', $code));
        }

        // No need to load slots if shipping method has no slot configuration
        if (!$shippingMethod->getShippingSlotConfig()) {
            return new JsonResponse(['code' => $code]);
        }

        return new JsonResponse([
            'code' => $code,
            'form_html' => '',
        ]);
    }
}
