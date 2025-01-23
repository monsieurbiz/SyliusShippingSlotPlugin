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

use Doctrine\ORM\Mapping as ORM;

trait ProductVariantTrait
{
    /**
     * @ORM\Column(name="preparation_delay", type="integer", nullable=true)
     */
    #[ORM\Column(name: 'preparation_delay', type: 'integer', nullable: true)]
    private ?int $preparationDelay = null;

    public function getPreparationDelay(): ?int
    {
        return $this->preparationDelay;
    }

    public function setPreparationDelay(?int $preparationDelay): void
    {
        $this->preparationDelay = $preparationDelay;
    }
}
