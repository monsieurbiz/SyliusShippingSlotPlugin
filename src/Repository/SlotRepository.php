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
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class SlotRepository extends EntityRepository implements SlotRepositoryInterface
{
    public function findByMethodAndStartDate(ShippingMethodInterface $shippingMethod, ?DateTimeInterface $from = null): array
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->addSelect('shipment')
            ->leftJoin('o.shipment', 'shipment')
            ->where('shipment.method = :shippingMethod')
            ->setParameter('shippingMethod', $shippingMethod)
        ;

        if ($from) {
            $queryBuilder
                ->andWhere('o.timestamp >= :from')
                ->setParameter('from', $from)
            ;
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function findByMethodAndDate(ShippingMethodInterface $shippingMethod, ?DateTimeInterface $from = null): array
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->addSelect('shipment')
            ->leftJoin('o.shipment', 'shipment')
            ->where('shipment.method = :shippingMethod')
            ->setParameter('shippingMethod', $shippingMethod)
        ;

        if ($from) {
            $queryBuilder
                ->andWhere('o.timestamp = :from')
                ->setParameter('from', $from)
            ;
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
