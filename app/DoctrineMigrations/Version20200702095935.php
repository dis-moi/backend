<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200702095935 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            CREATE TABLE relay (
                notice_id INT NOT NULL, 
                contributor_id INT NOT NULL,
                relayed_at DATETIME NOT NULL, 
                PRIMARY KEY(contributor_id, notice_id)
            )
            DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci 
            ENGINE = InnoDB
        ');
        $this->addSql('ALTER TABLE relay ADD CONSTRAINT FK_5D3AE2B97A19A357 FOREIGN KEY (contributor_id) REFERENCES contributor (id)');
        $this->addSql('ALTER TABLE relay ADD CONSTRAINT FK_5D3AE2B97D540AB FOREIGN KEY (notice_id) REFERENCES notice (id)');
        $this->addSql('CREATE INDEX INDEX_RELAY_CONTRIBUTOR ON relay (contributor_id)');
        $this->addSql('CREATE INDEX INDEX_RELAY_NOTICE ON relay (notice_id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE relay');
    }
}
