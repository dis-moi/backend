<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160802144526 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE recommendation DROP FOREIGN KEY FK_433224D2953C1C61');
        $this->addSql('ALTER TABLE recommendation ADD visibility VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE recommendation ADD CONSTRAINT FK_433224D2953C1C61 FOREIGN KEY (source_id) REFERENCES source (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE recommendation DROP FOREIGN KEY FK_433224D2953C1C61');
        $this->addSql('ALTER TABLE recommendation DROP visibility');
        $this->addSql('ALTER TABLE recommendation ADD CONSTRAINT FK_433224D2953C1C61 FOREIGN KEY (source_id) REFERENCES source (id) ON DELETE CASCADE');
    }
}
