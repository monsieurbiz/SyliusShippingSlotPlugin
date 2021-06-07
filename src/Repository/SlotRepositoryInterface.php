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

namespace MonsieurBiz\SyliusShippingSlotPlugin\Repository;

use DateTimeInterface;
use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShippingMethodInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface SlotRepositoryInterface extends RepositoryInterface
{
    /**
     * @return array
     */
    public function findByMethodAndStartDate(ShippingMethodInterface $shippingMethod, ?DateTimeInterface $from = null): array;

    /**
     * @return array
     */
    public function findByMethodAndDate(ShippingMethodInterface $shippingMethod, ?DateTimeInterface $date = null): array;
}
