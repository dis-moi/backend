<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160930142225 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE filter RENAME criterion');
        $this->addSql('ALTER TABLE recommendation_filter RENAME recommendation_criterion');
        $this->addSql('ALTER TABLE recommendation_criterion DROP FOREIGN KEY FK_74D6F1E7D395B25E');
        $this->addSql('ALTER TABLE recommendation_criterion CHANGE filter_id criterion_id INT NOT NULL');
        $this->addSql('ALTER TABLE recommendation_criterion ADD CONSTRAINT FK_74D6F1E7D395B25E FOREIGN KEY (criterion_id) REFERENCES criterion (id) ON DELETE CASCADE');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE criterion RENAME filter');
        $this->addSql('ALTER TABLE recommendation_criterion RENAME recommendation_filter');
        $this->addSql('ALTER TABLE recommendation_filter DROP FOREIGN KEY FK_74D6F1E7D395B25E');
        $this->addSql('ALTER TABLE recommendation_filter CHANGE criterion_id filter_id INT NOT NULL');
        $this->addSql('ALTER TABLE recommendation_filter ADD CONSTRAINT FK_74D6F1E7D395B25E FOREIGN KEY (filter_id) REFERENCES filter (id) ON DELETE CASCADE');
    }
}
