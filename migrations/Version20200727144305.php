<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200727144305 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE domain_name MODIFY path VARCHAR(255) NULL;');
        $this->addSql('UPDATE domain_name SET path = :newPath where path = :existingPath', ['newPath' => null, 'existingPath' => '/']);
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE domain_name SET path = :newPath where path is null', ['newPath' => '/']);
        $this->addSql('ALTER TABLE domain_name MODIFY path VARCHAR(255) NOT NULL;');
    }
}
