<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210519132610 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $localDefault = \Locale::getDefault();

        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE contributor ADD locale VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE notice ADD locale VARCHAR(255) DEFAULT NULL');
        $this->addSql("UPDATE contributor SET locale = '{$localDefault}' WHERE locale is NULL");
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE contributor DROP locale');
        $this->addSql('ALTER TABLE notice DROP locale');
    }
}
