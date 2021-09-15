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

namespace MonsieurBiz\SyliusShippingSlotPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210915210206 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Cascade shipment deletion to slot deletion. Also remove slots without shipment.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DELETE FROM monsieurbiz_shipping_slot_slot WHERE shipment_id IS NULL');
        $this->addSql('ALTER TABLE monsieurbiz_shipping_slot_slot DROP FOREIGN KEY FK_3BD6F1F67BE036FC');
        $this->addSql('ALTER TABLE monsieurbiz_shipping_slot_slot ADD CONSTRAINT FK_3BD6F1F67BE036FC FOREIGN KEY (shipment_id) REFERENCES sylius_shipment (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE monsieurbiz_shipping_slot_slot DROP FOREIGN KEY FK_3BD6F1F67BE036FC');
        $this->addSql('ALTER TABLE monsieurbiz_shipping_slot_slot ADD CONSTRAINT FK_3BD6F1F67BE036FC FOREIGN KEY (shipment_id) REFERENCES sylius_shipment (id) ON DELETE SET NULL');
    }
}
