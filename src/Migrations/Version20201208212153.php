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

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201208212153 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Setup the slot_config table and the relation with the shipping method.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE monsieurbiz_shipping_slot_config (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, rrules LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', preparation_delay INT DEFAULT NULL, pickup_delay INT DEFAULT NULL, duration_range INT DEFAULT NULL, available_spots INT DEFAULT NULL, color VARCHAR(10) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_shipping_method ADD shipping_slot_config_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_shipping_method ADD CONSTRAINT FK_5FB0EE113890C4F5 FOREIGN KEY (shipping_slot_config_id) REFERENCES monsieurbiz_shipping_slot_config (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_5FB0EE113890C4F5 ON sylius_shipping_method (shipping_slot_config_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sylius_shipping_method DROP FOREIGN KEY FK_5FB0EE113890C4F5');
        $this->addSql('DROP TABLE monsieurbiz_shipping_slot_config');
        $this->addSql('DROP INDEX IDX_5FB0EE113890C4F5 ON sylius_shipping_method');
        $this->addSql('ALTER TABLE sylius_shipping_method DROP shipping_slot_config_id');
    }
}
