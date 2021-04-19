<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * ManyToMany between Contributors and Users to allow Users to impersonate Contributors
 */
final class Version20210419184634 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        //$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE contributor_user (contributor_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_5EDB83077A19A357 (contributor_id), INDEX IDX_5EDB8307A76ED395 (user_id), PRIMARY KEY(contributor_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contributor_user ADD CONSTRAINT FK_5EDB83077A19A357 FOREIGN KEY (contributor_id) REFERENCES contributor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contributor_user ADD CONSTRAINT FK_5EDB8307A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE contributor_user');
    }
}
