<?php
namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20181228130847 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // rename tables
        $this->addSql('RENAME TABLE recommendation TO notice');
        $this->addSql('RENAME TABLE recommendation_channel TO notice_channel');
        $this->addSql('RENAME TABLE criterion TO type');
        $this->addSql('RENAME TABLE feedback TO rating');

        // rename links

        $this->addSql('ALTER TABLE matching_context CHANGE recommendation_id notice_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE matching_context ADD CONSTRAINT FK_665341C17D540AB FOREIGN KEY (notice_id) REFERENCES notice (id)');
        $this->addSql('CREATE INDEX IDX_665341C17D540AB ON matching_context (notice_id)');

        $this->addSql('ALTER TABLE rating DROP FOREIGN KEY FK_D2294458D173940B');
        $this->addSql('DROP INDEX IDX_D2294458D173940B ON rating');
        $this->addSql('ALTER TABLE rating CHANGE recommendation_id notice_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D22944587D540AB FOREIGN KEY (notice_id) REFERENCES notice (id)');
        $this->addSql('CREATE INDEX IDX_D22944587D540AB ON rating (notice_id)');


    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('RENAME TABLE notice TO recommendation');
        $this->addSql('RENAME TABLE notice_channel TO recommendation_channel');
        $this->addSql('RENAME TABLE type TO criterion');
        $this->addSql('RENAME TABLE rating TO feedback');

        $this->addSql('ALTER TABLE matching_context CHANGE notice_id recommendation_id INT DEFAULT NULL');
        $this->addSql('DROP INDEX IDX_665341C17D540AB ON matching_context');
        $this->addSql('ALTER TABLE matching_context DROP FOREIGN KEY FK_665341C17D540AB');

        $this->addSql('ALTER TABLE feedback DROP FOREIGN KEY FK_D22944587D540AB');
        $this->addSql('DROP INDEX IDX_D22944587D540AB ON feedback');
        $this->addSql('ALTER TABLE feedback CHANGE notice_id recommendation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE feedback ADD CONSTRAINT FK_D2294458D173940B FOREIGN KEY (recommendation_id) REFERENCES recommendation (id)');
        $this->addSql('CREATE INDEX IDX_D2294458D173940B ON feedback (recommendation_id)');
    }
}
