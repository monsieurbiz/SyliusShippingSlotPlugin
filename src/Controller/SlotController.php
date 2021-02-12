<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusShippingSlotPlugin\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class SlotController extends AbstractController
{
    public function listAction()
    {
        return new JsonResponse([
            // 'code' => $uiElement->getCode(),
            // 'form_html' => $this->renderView($uiElement->getAdminFormTemplate(), [
            //     'form' => $form->createView(),
            //     'uiElement' => $uiElement,
            //     'data' => $data,
            //     'isEdition' => (int) $isEdition,
            // ]),
        ]);
    }
}
