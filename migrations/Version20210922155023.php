<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * On custom index and foreign key names:
 * https://stackoverflow.com/questions/7623958/naming-a-relation-in-doctrine-2-orm.
 */
final class Version20210922155023 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name LONGTEXT DEFAULT NULL, alternate_name LONGTEXT DEFAULT NULL, description LONGTEXT DEFAULT NULL, category LONGTEXT DEFAULT NULL, offer_available_at_or_from LONGTEXT DEFAULT NULL, offer_price DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE matching_context ADD product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE matching_context ADD CONSTRAINT FK_matching_context_product_id FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_matching_context_product_id ON matching_context (product_id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE matching_context DROP FOREIGN KEY FK_matching_context_product_id');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP INDEX UNIQ_matching_context_product_id ON matching_context');
        $this->addSql('ALTER TABLE matching_context DROP product_id');
    }
}
