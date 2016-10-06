<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160930145820 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE recommendation CHANGE contributor_id contributor_id INT NOT NULL');
        $this->addSql('ALTER TABLE recommendation_criterion RENAME INDEX idx_74d6f1e7d173940b TO IDX_7EC3453D173940B');
        $this->addSql('ALTER TABLE recommendation_criterion RENAME INDEX idx_74d6f1e7d395b25e TO IDX_7EC345397766307');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE recommendation CHANGE contributor_id contributor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE recommendation_criterion RENAME INDEX idx_7ec3453d173940b TO IDX_74D6F1E7D173940B');
        $this->addSql('ALTER TABLE recommendation_criterion RENAME INDEX idx_7ec345397766307 TO IDX_74D6F1E7D395B25E');
    }
}
