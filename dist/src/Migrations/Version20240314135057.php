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

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240314135057 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE monsieurbiz_shipping_slot_shipping_method (shipping_method_id INT NOT NULL, shipping_slot_config_id INT NOT NULL, INDEX IDX_57D36B055F7D6850 (shipping_method_id), INDEX IDX_57D36B053890C4F5 (shipping_slot_config_id), PRIMARY KEY(shipping_method_id, shipping_slot_config_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE monsieurbiz_shipping_slot_shipping_method ADD CONSTRAINT FK_57D36B055F7D6850 FOREIGN KEY (shipping_method_id) REFERENCES sylius_shipping_method (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE monsieurbiz_shipping_slot_shipping_method ADD CONSTRAINT FK_57D36B053890C4F5 FOREIGN KEY (shipping_slot_config_id) REFERENCES monsieurbiz_shipping_slot_config (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE monsieurbiz_shipping_slot_shipping_method DROP FOREIGN KEY FK_57D36B055F7D6850');
        $this->addSql('ALTER TABLE monsieurbiz_shipping_slot_shipping_method DROP FOREIGN KEY FK_57D36B053890C4F5');
        $this->addSql('DROP TABLE monsieurbiz_shipping_slot_shipping_method');
    }
}
