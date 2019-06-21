<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190621142609 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE alternative');
        $this->addSql('ALTER TABLE notice DROP title');
        $this->addSql('ALTER TABLE contributor ADD intro VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE alternative (id INT AUTO_INCREMENT NOT NULL, recommendation_id INT DEFAULT NULL, urlToRedirect VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, `label` VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, description VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, INDEX IDX_EFF5DFAD173940B (recommendation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE alternative ADD CONSTRAINT FK_EFF5DFAD173940B FOREIGN KEY (recommendation_id) REFERENCES notice (id)');
        $this->addSql('ALTER TABLE contributor DROP intro');
        $this->addSql('ALTER TABLE notice ADD title VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
    }
}
