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

namespace MonsieurBiz\SyliusShippingSlotPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240314124625 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE monsieurbiz_shipping_slot_slot ADD shipping_slot_config_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE monsieurbiz_shipping_slot_slot ADD CONSTRAINT FK_3BD6F1F63890C4F5 FOREIGN KEY (shipping_slot_config_id) REFERENCES monsieurbiz_shipping_slot_config (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_3BD6F1F63890C4F5 ON monsieurbiz_shipping_slot_slot (shipping_slot_config_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE monsieurbiz_shipping_slot_slot DROP FOREIGN KEY FK_3BD6F1F63890C4F5');
        $this->addSql('DROP INDEX IDX_3BD6F1F63890C4F5 ON monsieurbiz_shipping_slot_slot');
        $this->addSql('ALTER TABLE monsieurbiz_shipping_slot_slot DROP shipping_slot_config_id');
    }
}
