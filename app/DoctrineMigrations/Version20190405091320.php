<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190405091320 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notice DROP FOREIGN KEY FK_480D45C2C54C8C93');
        $this->addSql('ALTER TABLE notice_type RENAME notice_intention');
        $this->addSql('DROP INDEX IDX_480D45C24BA9772C ON notice');
        $this->addSql('ALTER TABLE notice CHANGE notice_type_id notice_intention_id INT NOT NULL');
        $this->addSql('ALTER TABLE notice ADD CONSTRAINT FK_480D45C247250181 FOREIGN KEY (notice_intention_id) REFERENCES notice_intention (id)');
        $this->addSql('CREATE INDEX IDX_480D45C247250181 ON notice (notice_intention_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notice DROP FOREIGN KEY FK_480D45C247250181');
        $this->addSql('ALTER TABLE notice_intention RENAME notice_type');
        $this->addSql('DROP INDEX IDX_480D45C247250181 ON notice');
        $this->addSql('ALTER TABLE notice CHANGE notice_intention_id notice_type_id INT NOT NULL');
        $this->addSql('ALTER TABLE notice ADD CONSTRAINT FK_480D45C2C54C8C93 FOREIGN KEY (notice_type_id) REFERENCES notice_type (id)');
        $this->addSql('CREATE INDEX IDX_480D45C24BA9772C ON notice (notice_type_id)');
    }
}
