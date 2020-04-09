<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161006170904 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' != $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE editor (id INT AUTO_INCREMENT NOT NULL, `label` VARCHAR(255) NOT NULL, url VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE recommendation_criterion DROP FOREIGN KEY FK_74D6F1E7D395B25E');
        $this->addSql('ALTER TABLE recommendation_criterion DROP FOREIGN KEY FK_74D6F1E7D173940B');
        $this->addSql('DROP INDEX idx_74d6f1e7d173940b ON recommendation_criterion');
        $this->addSql('CREATE INDEX IDX_7EC3453D173940B ON recommendation_criterion (recommendation_id)');
        $this->addSql('DROP INDEX idx_74d6f1e7d395b25e ON recommendation_criterion');
        $this->addSql('CREATE INDEX IDX_7EC345397766307 ON recommendation_criterion (criterion_id)');
        $this->addSql('ALTER TABLE recommendation_criterion ADD CONSTRAINT FK_74D6F1E7D395B25E FOREIGN KEY (criterion_id) REFERENCES criterion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recommendation_criterion ADD CONSTRAINT FK_74D6F1E7D173940B FOREIGN KEY (recommendation_id) REFERENCES recommendation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE resource DROP FOREIGN KEY FK_5F8A7F73D173940B');
        $this->addSql('DROP INDEX uniq_5f8a7f73d173940b ON resource');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BC91F416D173940B ON resource (recommendation_id)');
        $this->addSql('ALTER TABLE resource ADD CONSTRAINT FK_5F8A7F73D173940B FOREIGN KEY (recommendation_id) REFERENCES recommendation (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' != $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE editor');
        $this->addSql('ALTER TABLE recommendation_criterion DROP FOREIGN KEY FK_7EC3453D173940B');
        $this->addSql('ALTER TABLE recommendation_criterion DROP FOREIGN KEY FK_7EC345397766307');
        $this->addSql('DROP INDEX idx_7ec3453d173940b ON recommendation_criterion');
        $this->addSql('CREATE INDEX IDX_74D6F1E7D173940B ON recommendation_criterion (recommendation_id)');
        $this->addSql('DROP INDEX idx_7ec345397766307 ON recommendation_criterion');
        $this->addSql('CREATE INDEX IDX_74D6F1E7D395B25E ON recommendation_criterion (criterion_id)');
        $this->addSql('ALTER TABLE recommendation_criterion ADD CONSTRAINT FK_7EC3453D173940B FOREIGN KEY (recommendation_id) REFERENCES recommendation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recommendation_criterion ADD CONSTRAINT FK_7EC345397766307 FOREIGN KEY (criterion_id) REFERENCES criterion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE resource DROP FOREIGN KEY FK_BC91F416D173940B');
        $this->addSql('DROP INDEX uniq_bc91f416d173940b ON resource');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5F8A7F73D173940B ON resource (recommendation_id)');
        $this->addSql('ALTER TABLE resource ADD CONSTRAINT FK_BC91F416D173940B FOREIGN KEY (recommendation_id) REFERENCES recommendation (id)');
    }
}
