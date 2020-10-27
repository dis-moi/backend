<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160701123616 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' != $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE contributor DROP FOREIGN KEY FK_DA6F9793D173940B');
        $this->addSql('DROP INDEX UNIQ_DA6F9793D173940B ON contributor');
        $this->addSql('ALTER TABLE contributor DROP recommendation_id');
        $this->addSql('ALTER TABLE recommendation DROP INDEX UNIQ_433224D27A19A357, ADD INDEX IDX_433224D27A19A357 (contributor_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' != $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE contributor ADD recommendation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE contributor ADD CONSTRAINT FK_DA6F9793D173940B FOREIGN KEY (recommendation_id) REFERENCES recommendation (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DA6F9793D173940B ON contributor (recommendation_id)');
        $this->addSql('ALTER TABLE recommendation DROP INDEX IDX_433224D27A19A357, ADD UNIQUE INDEX UNIQ_433224D27A19A357 (contributor_id)');
    }
}
