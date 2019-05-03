<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190503061621 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notice ADD intention VARCHAR(255) DEFAULT \'other\' NOT NULL');
        $this->addSql('UPDATE notice n JOIN notice_intention i ON n.notice_intention_id = i.id SET n.intention = i.slug');
        $this->addSql('ALTER TABLE notice DROP FOREIGN KEY FK_480D45C247250181');
        $this->addSql('DROP TABLE notice_intention');
        $this->addSql('DROP INDEX IDX_480D45C247250181 ON notice');
        $this->addSql('ALTER TABLE notice DROP notice_intention_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE notice_intention (id INT AUTO_INCREMENT NOT NULL, `label` VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, slug VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE notice ADD notice_intention_id INT NOT NULL');
        $this->addSql('ALTER TABLE notice ADD CONSTRAINT FK_480D45C247250181 FOREIGN KEY (notice_intention_id) REFERENCES notice_intention (id)');
        $this->addSql('INSERT INTO notice_intention (label, slug) SELECT intention,  intention FROM notice GROUP BY intention');
        $this->addSql('UPDATE notice n JOIN notice_intention i ON n.intention = i.slug SET n.notice_intention_id = i.id');
        $this->addSql('CREATE INDEX IDX_480D45C247250181 ON notice (notice_intention_id)');
        $this->addSql('ALTER TABLE notice DROP intention');
    }
}
