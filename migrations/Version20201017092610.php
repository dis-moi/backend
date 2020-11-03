<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201017092610 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            CREATE TABLE pin (
                contributor_id INT NOT NULL,
                notice_id INT NOT NULL,
                `rank` INT NOT NULL,
                PRIMARY KEY(contributor_id, notice_id)
            )
            DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci
            ENGINE = InnoDB
        ');
        $this->addSql('ALTER TABLE pin ADD CONSTRAINT FK_PIN_CONTRIBUTOR FOREIGN KEY (contributor_id) REFERENCES contributor (id)');
        $this->addSql('ALTER TABLE pin ADD CONSTRAINT FK_PIN_NOTICE FOREIGN KEY (notice_id) REFERENCES notice (id)');
        $this->addSql('CREATE INDEX INDEX_PIN_CONTRIBUTOR ON pin (contributor_id)');
        $this->addSql('CREATE INDEX INDEX_PIN_NOTICE ON pin (notice_id)');

        $this->addSql('INSERT INTO pin (contributor_id, notice_id, `rank`) SELECT id, starred_notice, 0 FROM contributor WHERE starred_notice IS NOT NULL');

        $this->addSql('ALTER TABLE contributor DROP FOREIGN KEY starred_notice___fk');
        $this->addSql('ALTER TABLE contributor DROP COLUMN starred_notice');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE contributor ADD COLUMN starred_notice int(11) NULL');
        $this->addSql('ALTER TABLE contributor ADD CONSTRAINT starred_notice___fk FOREIGN KEY (starred_notice) REFERENCES kraftbackenddev.notice (id)');
        $this->addSql('DROP TABLE pin');
    }
}
