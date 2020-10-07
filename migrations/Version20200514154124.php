<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200514154124 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('alter table contributor add email varchar(255) null after name');
        $this->addSql('alter table contributor modify enabled tinyint(1) not null after intro');
        $this->addSql('alter table contributor add website varchar(255) null after email');
        $this->addSql('create unique index contributor_email_uindex on contributor (email)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX contributor_email_uindex ON contributor');
        $this->addSql('ALTER TABLE contributor DROP email, DROP website');
    }
}
