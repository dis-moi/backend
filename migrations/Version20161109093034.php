<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161109093034 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' != $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE criterion ADD slug VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE criterion SET slug=label');
        $this->addSql('UPDATE criterion SET label=description');
        $this->addSql('ALTER TABLE criterion DROP description');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' != $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE criterion ADD description LONGTEXT DEFAULT NULL');
        $this->addSql('UPDATE criterion SET description=label');
        $this->addSql('UPDATE criterion SET label=slug');
        $this->addSql('ALTER TABLE criterion DROP slug');
    }
}
