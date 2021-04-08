<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160808112313 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' != $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE recommendation DROP FOREIGN KEY FK_433224D2953C1C61');
        $this->addSql('DROP INDEX UNIQ_433224D2953C1C61 ON recommendation');
        $this->addSql('ALTER TABLE recommendation DROP source_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' != $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE recommendation ADD source_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE recommendation ADD CONSTRAINT FK_433224D2953C1C61 FOREIGN KEY (source_id) REFERENCES source (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_433224D2953C1C61 ON recommendation (source_id)');
    }
}
