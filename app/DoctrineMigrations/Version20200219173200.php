<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200219173200 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            ALTER TABLE `domain_name`
                CHANGE COLUMN createdAt created_at DATETIME,
                CHANGE COLUMN updatedAt updated_at DATETIME
            ;');
        $this->addSql('
            ALTER TABLE `domains_set` 
                CHANGE COLUMN createdAt created_at DATETIME,
                CHANGE COLUMN updatedAt updated_at DATETIME
            ;');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

      $this->addSql('
            ALTER TABLE `domain_name`
                CHANGE COLUMN created_at createdAt DATETIME,
                CHANGE COLUMN updated_at updatedAt DATETIME
            ;');
      $this->addSql('
            ALTER TABLE `domains_set` 
                CHANGE COLUMN created_at createdAt DATETIME,
                CHANGE COLUMN updated_at updatedAt DATETIME
            ;');
    }
}
