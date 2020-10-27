<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190112144348 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE source DROP FOREIGN KEY FK_BC91F4166995AC4C');
        $this->addSql('DROP TABLE editor');

        $this->addSql('ALTER TABLE source DROP FOREIGN KEY FK_5F8A7F73D173940B');
        $this->addSql('DROP INDEX IDX_BC91F4166995AC4C ON source');
        $this->addSql('DROP INDEX UNIQ_BC91F416D173940B ON source');

        $this->addSql('ALTER TABLE source CHANGE recommendation_id notice_id INT DEFAULT NULL, DROP editor_id, DROP description');
        $this->addSql('ALTER TABLE source ADD CONSTRAINT FK_5F8A7F737D540AB FOREIGN KEY (notice_id) REFERENCES notice (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5F8A7F737D540AB ON source (notice_id)');

        $this->addSql('ALTER TABLE notice DROP FOREIGN KEY FK_480D45C2C54C8C93');
        $this->addSql('DROP INDEX idx_480d45c2c54c8c93 ON notice');
        $this->addSql('CREATE INDEX IDX_480D45C24BA9772C ON notice (notice_type_id)');
        $this->addSql('ALTER TABLE notice ADD CONSTRAINT FK_480D45C2C54C8C93 FOREIGN KEY (notice_type_id) REFERENCES notice_type (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE editor (id INT AUTO_INCREMENT NOT NULL, `label` VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, url VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE notice DROP FOREIGN KEY FK_480D45C24BA9772C');
        $this->addSql('ALTER TABLE notice CHANGE visibility visibility VARCHAR(255) DEFAULT \'\'private\'\' NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('DROP INDEX idx_480d45c24ba9772c ON notice');
        $this->addSql('CREATE INDEX IDX_480D45C2C54C8C93 ON notice (notice_type_id)');
        $this->addSql('ALTER TABLE notice ADD CONSTRAINT FK_480D45C24BA9772C FOREIGN KEY (notice_type_id) REFERENCES notice_type (id)');
        $this->addSql('ALTER TABLE source DROP FOREIGN KEY FK_5F8A7F737D540AB');
        $this->addSql('DROP INDEX UNIQ_5F8A7F737D540AB ON source');
        $this->addSql('ALTER TABLE source ADD recommendation_id INT DEFAULT NULL, ADD editor_id INT DEFAULT NULL, ADD description VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, DROP notice_id');
        $this->addSql('ALTER TABLE source ADD CONSTRAINT FK_5F8A7F73D173940B FOREIGN KEY (recommendation_id) REFERENCES notice (id)');
        $this->addSql('ALTER TABLE source ADD CONSTRAINT FK_BC91F4166995AC4C FOREIGN KEY (editor_id) REFERENCES editor (id)');
        $this->addSql('CREATE INDEX IDX_BC91F4166995AC4C ON source (editor_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BC91F416D173940B ON source (recommendation_id)');
    }
}
