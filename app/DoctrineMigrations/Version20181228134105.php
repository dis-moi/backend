<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181228134105 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE alternative_copy');
        $this->addSql('DROP TABLE resource_copy');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE alternative_copy (id INT AUTO_INCREMENT NOT NULL, recommendation_id INT DEFAULT NULL, urlToRedirect VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, `label` VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, description VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, INDEX IDX_EFF5DFAD173940B (recommendation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE resource_copy (id INT AUTO_INCREMENT NOT NULL, recommendation_id INT DEFAULT NULL, `label` VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, description VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, url VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, editor_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_BC91F416D173940B (recommendation_id), INDEX IDX_BC91F4166995AC4C (editor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
    }
}
