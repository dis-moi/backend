<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181228142357 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE contributor DROP organization, DROP updated_at, DROP image');

        $this->addSql('DROP INDEX IDX_665341C1D173940B ON matching_context');
        $this->addSql('ALTER TABLE matching_context CHANGE notice_id notice_id INT DEFAULT NULL, CHANGE description description VARCHAR(255) DEFAULT NULL, CHANGE excludeUrlRegex excludeUrlRegex VARCHAR(255) DEFAULT NULL');

        $this->addSql('ALTER TABLE notice_channel DROP FOREIGN KEY FK_FF9A87EBD173940B');
        $this->addSql('DROP INDEX IDX_FF9A87EBD173940B ON notice_channel');
        $this->addSql('ALTER TABLE notice_channel DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE notice_channel DROP FOREIGN KEY FK_FF9A87EB72F5A1AA');
        $this->addSql('ALTER TABLE notice_channel CHANGE recommendation_id notice_id INT NOT NULL');
        $this->addSql('ALTER TABLE notice_channel ADD CONSTRAINT FK_2C1C75F87D540AB FOREIGN KEY (notice_id) REFERENCES notice (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_2C1C75F87D540AB ON notice_channel (notice_id)');
        $this->addSql('ALTER TABLE notice_channel ADD PRIMARY KEY (notice_id, channel_id)');
        $this->addSql('DROP INDEX idx_ff9a87eb72f5a1aa ON notice_channel');
        $this->addSql('CREATE INDEX IDX_2C1C75F872F5A1AA ON notice_channel (channel_id)');
        $this->addSql('ALTER TABLE notice_channel ADD CONSTRAINT FK_FF9A87EB72F5A1AA FOREIGN KEY (channel_id) REFERENCES channel (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE contributor ADD organization VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD updated_at DATETIME DEFAULT \'NULL\', ADD image VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
        $this->addSql('CREATE INDEX IDX_665341C1D173940B ON matching_context (notice_id)');
        $this->addSql('ALTER TABLE notice_channel DROP FOREIGN KEY FK_2C1C75F87D540AB');
        $this->addSql('DROP INDEX IDX_2C1C75F87D540AB ON notice_channel');
        $this->addSql('ALTER TABLE notice_channel DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE notice_channel DROP FOREIGN KEY FK_2C1C75F872F5A1AA');
        $this->addSql('ALTER TABLE notice_channel CHANGE notice_id recommendation_id INT NOT NULL');
        $this->addSql('ALTER TABLE notice_channel ADD CONSTRAINT FK_FF9A87EBD173940B FOREIGN KEY (recommendation_id) REFERENCES notice (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_FF9A87EBD173940B ON notice_channel (recommendation_id)');
        $this->addSql('ALTER TABLE notice_channel ADD PRIMARY KEY (recommendation_id, channel_id)');
        $this->addSql('DROP INDEX idx_2c1c75f872f5a1aa ON notice_channel');
        $this->addSql('CREATE INDEX IDX_FF9A87EB72F5A1AA ON notice_channel (channel_id)');
        $this->addSql('ALTER TABLE notice_channel ADD CONSTRAINT FK_2C1C75F872F5A1AA FOREIGN KEY (channel_id) REFERENCES channel (id) ON DELETE CASCADE');
    }
}
