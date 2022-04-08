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

use Recurr\Recurrence;
use Symfony\Contracts\EventDispatcher\Event;

final class RecurrenceGenerationEvent extends Event
{
    private array $recurrences;

    public function __construct(array $recurrences)
    {
        $this->recurrences = $recurrences;
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
}
