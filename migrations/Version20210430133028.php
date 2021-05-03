<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * ManyToMany between Contributors and Users to allow Users to impersonate Contributors
 */
final class Version20210430133028 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE user_contributor (user_id INT NOT NULL, contributor_id INT NOT NULL, INDEX IDX_73745E06A76ED395 (user_id), INDEX IDX_73745E067A19A357 (contributor_id), PRIMARY KEY(user_id, contributor_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_contributor ADD CONSTRAINT FK_73745E06A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_contributor ADD CONSTRAINT FK_73745E067A19A357 FOREIGN KEY (contributor_id) REFERENCES contributor (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE user_contributor');
    }
}
