<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170526142717 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE channel (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recommendation_channel (recommendation_id INT NOT NULL, channel_id INT NOT NULL, INDEX IDX_FF9A87EBD173940B (recommendation_id), INDEX IDX_FF9A87EB72F5A1AA (channel_id), PRIMARY KEY(recommendation_id, channel_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE recommendation_channel ADD CONSTRAINT FK_FF9A87EBD173940B FOREIGN KEY (recommendation_id) REFERENCES recommendation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recommendation_channel ADD CONSTRAINT FK_FF9A87EB72F5A1AA FOREIGN KEY (channel_id) REFERENCES channel (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE recommendation_channel DROP FOREIGN KEY FK_FF9A87EB72F5A1AA');
        $this->addSql('DROP TABLE channel');
        $this->addSql('DROP TABLE recommendation_channel');
    }
}
