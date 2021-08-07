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

namespace MonsieurBiz\SyliusShippingSlotPlugin\MessageHandler;

use MonsieurBiz\SyliusSalesReportsPlugin\Event\ShipmentPaidEvent;
use MonsieurBiz\SyliusShippingSlotPlugin\Message\ShipmentMessage;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ShipmentMessageHandler implements MessageHandlerInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(ShipmentMessage $message): void
    {
        $this->eventDispatcher->dispatch(new ShipmentPaidEvent(
            $message->getShipmentId()
        ));
    }
}
