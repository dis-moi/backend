<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160614150330 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE contributor (id INT AUTO_INCREMENT NOT NULL, recommendation_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, organization VARCHAR(255) NOT NULL, updated_at DATETIME DEFAULT NULL, image VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_DA6F9793D173940B (recommendation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contributor ADD CONSTRAINT FK_DA6F9793D173940B FOREIGN KEY (recommendation_id) REFERENCES recommendation (id)');
        $this->addSql('ALTER TABLE recommendation ADD contributor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE recommendation ADD CONSTRAINT FK_433224D27A19A357 FOREIGN KEY (contributor_id) REFERENCES contributor (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_433224D27A19A357 ON recommendation (contributor_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE recommendation DROP FOREIGN KEY FK_433224D27A19A357');
        $this->addSql('DROP TABLE contributor');
        $this->addSql('DROP INDEX UNIQ_433224D27A19A357 ON recommendation');
        $this->addSql('ALTER TABLE recommendation DROP contributor_id');
    }
}
