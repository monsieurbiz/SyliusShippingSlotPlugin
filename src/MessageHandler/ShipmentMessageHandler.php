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

use MonsieurBiz\SyliusShippingSlotPlugin\Message\ShipmentMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ShipmentMessageHandler implements MessageHandlerInterface
{
    public function __invoke(ShipmentMessage $message): void
    {
        // @TODO Fire event
        dump($message);
    }
}
