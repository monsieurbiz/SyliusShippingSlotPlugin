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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait ShippingMethodTrait
{
    /**
     * @deprecated Use shippingSlotConfigs instead
     *
     * @ORM\ManyToOne(targetEntity="\MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShippingSlotConfigInterface")
     * @ORM\JoinColumn(name="shipping_slot_config_id", nullable=true, referencedColumnName="id", onDelete="SET NULL")
     */
    #[ORM\ManyToOne(targetEntity: ShippingSlotConfigInterface::class)]
    #[ORM\JoinColumn(name: 'shipping_slot_config_id', nullable: true, referencedColumnName: 'id', onDelete: 'SET NULL')]
    private ?ShippingSlotConfigInterface $shippingSlotConfig = null;

    /**
     * @ORM\ManyToMany(targetEntity="\MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShippingSlotConfigInterface")
     * @ORM\JoinTable(
     *     name="monsieurbiz_shipping_slot_shipping_method",
     *     joinColumns={@ORM\JoinColumn(name="shipping_method_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="shipping_slot_config_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    #[ORM\ManyToMany(targetEntity: ShippingSlotConfigInterface::class)]
    #[ORM\JoinTable(name: 'monsieurbiz_shipping_slot_shipping_method')]
    #[ORM\JoinColumn(name: 'shipping_method_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'shipping_slot_config_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Collection $shippingSlotConfigs;

    public function __construct()
    {
        $this->shippingSlotConfigs = new ArrayCollection();
    }

    public function getShippingSlotConfig(): ?ShippingSlotConfigInterface
    {
        return $this->shippingSlotConfig;
    }

    /**
     * @deprecated Use setShippingSlotConfigs instead
     */
    public function setShippingSlotConfig(?ShippingSlotConfigInterface $shippingSlotConfig): void
    {
        $this->shippingSlotConfig = $shippingSlotConfig;
    }

    public function getShippingSlotConfigs(): Collection
    {
        return $this->shippingSlotConfigs;
    }

    public function setShippingSlotConfigs(Collection $shippingSlotConfigs): void
    {
        $this->shippingSlotConfigs = $shippingSlotConfigs;
    }

    public function addShippingSlotConfig(ShippingSlotConfigInterface $shippingSlotConfig): void
    {
        if (!$this->hasShippingSlotConfig($shippingSlotConfig)) {
            $this->shippingSlotConfigs->add($shippingSlotConfig);
        }
    }

    public function removeShippingSlotConfig(ShippingSlotConfigInterface $shippingSlotConfig): void
    {
        if ($this->hasShippingSlotConfig($shippingSlotConfig)) {
            $this->shippingSlotConfigs->removeElement($shippingSlotConfig);
        }
    }

    public function hasShippingSlotConfig(ShippingSlotConfigInterface $shippingSlotConfig): bool
    {
        return $this->shippingSlotConfigs->contains($shippingSlotConfig);
    }
}
