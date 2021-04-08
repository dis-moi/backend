<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181229142424 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE rating DROP FOREIGN KEY FK_D22944587D540AB');
        $this->addSql('ALTER TABLE rating CHANGE context_datetime context_timestamp DATETIME DEFAULT NULL, ADD context_geolocation VARCHAR(255) DEFAULT NULL');
        $this->addSql('DROP INDEX idx_d22944587d540ab ON rating');
        $this->addSql('CREATE INDEX IDX_D88926227D540AB ON rating (notice_id)');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D22944587D540AB FOREIGN KEY (notice_id) REFERENCES notice (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE rating DROP FOREIGN KEY FK_D88926227D540AB');
        $this->addSql('ALTER TABLE rating CHANGE context_timestamp context_datetime DATETIME DEFAULT \'NULL\', DROP context_geolocation');
        $this->addSql('DROP INDEX idx_d88926227d540ab ON rating');
        $this->addSql('CREATE INDEX IDX_D22944587D540AB ON rating (notice_id)');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D88926227D540AB FOREIGN KEY (notice_id) REFERENCES notice (id)');
    }
}
