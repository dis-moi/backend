<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200116112234 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE extension (id VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, created DATETIME DEFAULT CURRENT_TIMESTAMP, updated DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscription (contributor_id INT NOT NULL, extension_id VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, created DATETIME DEFAULT CURRENT_TIMESTAMP, updated DATETIME DEFAULT NULL, UNIQUE (contributor_id, extension_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_SubscriptionContributor FOREIGN KEY (contributor_id) REFERENCES contributor (id)');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_SubscriptionExtension FOREIGN KEY (extension_id) REFERENCES extension(id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_SubscriptionContributor');
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_SubscriptionExtension');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('DROP TABLE extension');
    }
}
