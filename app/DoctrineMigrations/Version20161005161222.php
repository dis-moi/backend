<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161005161222 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE organization (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contributor ADD organization_id INT DEFAULT NULL, ADD user_id INT DEFAULT NULL, ADD role VARCHAR(255) DEFAULT \'author\' NOT NULL, DROP organization, CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE contributor ADD CONSTRAINT FK_DA6F979332C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE contributor ADD CONSTRAINT FK_DA6F9793A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('CREATE INDEX IDX_DA6F979332C8A3DE ON contributor (organization_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DA6F9793A76ED395 ON contributor (user_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE contributor DROP FOREIGN KEY FK_DA6F979332C8A3DE');
        $this->addSql('DROP TABLE organization');
        $this->addSql('DROP INDEX IDX_DA6F979332C8A3DE ON contributor');
        $this->addSql('DROP INDEX UNIQ_DA6F9793A76ED395 ON contributor');
        $this->addSql('ALTER TABLE contributor ADD organization VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, DROP organization_id, DROP user_id, DROP role, CHANGE image image VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
    }
}
