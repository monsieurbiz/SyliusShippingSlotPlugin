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

namespace MonsieurBiz\SyliusShippingSlotPlugin\Entity;

use Sylius\Component\Core\Model\ProductVariantInterface as SyliusProductVariantInterface;

interface ProductVariantInterface extends SyliusProductVariantInterface
{
    public function getPreparationDelay(): ?int;

    public function setPreparationDelay(?int $preparationDelay): void;
}
