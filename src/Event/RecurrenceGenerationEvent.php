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

namespace MonsieurBiz\SyliusShippingSlotPlugin\Event;

use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShippingMethodInterface;
use Recurr\Recurrence;
use Symfony\Contracts\EventDispatcher\Event;

final class RecurrenceGenerationEvent extends Event
{
    private array $recurrences;

    private ?ShippingMethodInterface $shippingMethod;

    public function __construct(array $recurrences, ?ShippingMethodInterface $shippingMethod = null)
    {
        $this->recurrences = $recurrences;
        $this->shippingMethod = $shippingMethod;
    }

    /**
     * @return Recurrence[]
     */
    public function getRecurrences(): array
    {
        return $this->recurrences;
    }

    public function setRecurrences(array $recurrences): void
    {
        $this->recurrences = $recurrences;
    }

    public function getShippingMethod(): ?ShippingMethodInterface
    {
        return $this->shippingMethod;
    }
}
