<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200917092610 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
//        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
//
//        $this->addSql('
//            CREATE TABLE pin (
//                contributor_id INT NOT NULL,
//                notice_id INT NOT NULL,
//                rank INT NOT NULL,
//                PRIMARY KEY(contributor_id, notice_id)
//            )
//            DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci
//            ENGINE = InnoDB
//        ');
//        $this->addSql('ALTER TABLE pin ADD CONSTRAINT FK_STARRED_NOTICE_CONTRIBUTOR FOREIGN KEY (contributor_id) REFERENCES contributor (id)');
//        $this->addSql('ALTER TABLE pin ADD CONSTRAINT FK_STARRED_NOTICE_NOTICE FOREIGN KEY (notice_id) REFERENCES notice (id)');
//        $this->addSql('CREATE INDEX INDEX_PIN_CONTRIBUTOR ON pin (contributor_id)');
//        $this->addSql('CREATE INDEX INDEX_PIN_NOTICE ON pin (notice_id)');
//
//        $this->addSql('ALTER TABLE contributor DROP COLUMN starred_notice');
    }

    public function down(Schema $schema): void
    {
//        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
//
//        $this->addSql('ALTER TABLE contributor DROP COLUMN pin');
    }
}
