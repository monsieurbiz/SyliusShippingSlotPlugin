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

namespace MonsieurBiz\SyliusShippingSlotPlugin\Listener;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{
    public function addAdminMenuItem(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        if (!$content = $menu->getChild('sales')) {
            $content = $menu
                ->addChild('sales')
                ->setLabel('sylius.menu.admin.main.sales.header')
            ;
        }

        $content->addChild('monsieurbiz-shipping-slot-slot', ['route' => 'monsieurbiz_shipping_slot_admin_slot_index'])
            ->setLabel('monsieurbiz_shipping_slot.ui.slots')
            ->setLabelAttribute('icon', 'clipboard list')
        ;
    }
}
