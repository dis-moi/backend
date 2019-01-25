<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20181228134252 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notice ADD notice_type_id INT NOT NULL, CHANGE description message LONGTEXT NOT NULL, CHANGE title title VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');

        $this->addSql('ALTER TABLE notice DROP FOREIGN KEY FK_433224D27A19A357');
        $this->addSql('DROP INDEX idx_433224d27a19a357 ON notice');
        $this->addSql('ALTER TABLE notice ADD CONSTRAINT FK_433224D27A19A357 FOREIGN KEY (contributor_id) REFERENCES contributor (id)');
        $this->addSql('CREATE INDEX IDX_480D45C27A19A357 ON notice (contributor_id)');
    }

    /**
     * Retrieve `recommendation_criterion` table data & setup new notice *-1 Type w/ the first criterion/type found
     * then delete the table
     */
    public function postUp(Schema $schema)
    {
        $data = $this->connection->createQueryBuilder()->select('*')
            ->from('recommendation_criterion', 'rc')
            ->execute()
            ->fetchAll();

        foreach ($data as $line) {
            $this->connection->exec(sprintf('UPDATE notice SET notice_type_id = %d WHERE id = %d', $line['criterion_id'], $line['recommendation_id']));
        }

        // default type Autre
        $this->connection->exec('INSERT INTO notice_type (label, slug) VALUES ("Autre", "autre")');
        $defaultTypeId = $this->connection->createQueryBuilder()->select('id')->from('notice_type', 't')
            ->where('t.slug = :slug')
            ->setParameter('slug', 'autre')
            ->execute()->fetchAll()[0]['id'];

        $this->connection->exec(sprintf('UPDATE notice SET notice_type_id = %d WHERE notice_type_id = 0', $defaultTypeId));

        $this->connection->exec('DROP TABLE recommendation_criterion');

        $this->connection->exec('ALTER TABLE notice ADD CONSTRAINT FK_480D45C2C54C8C93 FOREIGN KEY (notice_type_id) REFERENCES notice_type (id)');
        $this->connection->exec('CREATE INDEX IDX_480D45C2C54C8C93 ON notice (notice_type_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notice DROP FOREIGN KEY FK_480D45C2C54C8C93');
        $this->addSql('DROP INDEX IDX_480D45C2C54C8C93 ON notice');
        $this->addSql('ALTER TABLE notice DROP FOREIGN KEY FK_480D45C27A19A357');
        $this->addSql('ALTER TABLE notice CHANGE title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, DROP notice_type_id, CHANGE message description LONGTEXT NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('DROP INDEX idx_480d45c27a19a357 ON notice');
        $this->addSql('CREATE INDEX IDX_433224D27A19A357 ON notice (contributor_id)');
        $this->addSql('ALTER TABLE notice ADD CONSTRAINT FK_480D45C27A19A357 FOREIGN KEY (contributor_id) REFERENCES contributor (id)');

        $this->addSql('CREATE TABLE recommendation_criterion (recommendation_id INT NOT NULL, criterion_id INT NOT NULL, INDEX IDX_7EC345397766307 (criterion_id), INDEX IDX_7EC3453D173940B (recommendation_id), PRIMARY KEY(recommendation_id, criterion_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
    }
}
