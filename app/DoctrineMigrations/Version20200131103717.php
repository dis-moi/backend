<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200131103717 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            CREATE TABLE domain_name (
                id INT AUTO_INCREMENT NOT NULL, 
                name VARCHAR(255) NOT NULL, 
                createdAt DATETIME NOT NULL, 
                updatedAt DATETIME NOT NULL, 
                PRIMARY KEY(id),
                UNIQUE INDEX UNIQ_DOMAIN_NAME (name)
                            
            )
            DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci 
            ENGINE = InnoDB
        ');

        $this->addSql('
            CREATE TABLE matching_context_domain_name (
                matching_context_id INT NOT NULL, 
                domain_name_id INT NOT NULL, 
                PRIMARY KEY(matching_context_id, domain_name_id)
            )
            DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci 
            ENGINE = InnoDB
        ');

        $this->addSql('
              CREATE TABLE domains_set (
                  id INT AUTO_INCREMENT NOT NULL, 
                  name VARCHAR(255) NOT NULL, 
                  createdAt DATETIME NOT NULL,
                  updatedAt DATETIME NOT NULL,
                  PRIMARY KEY(id),
                  UNIQUE INDEX UNIQ_DOMAINS_SET_NAME (name)
              )
              DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci 
              ENGINE = InnoDB
          ');

        $this->addSql('
              CREATE TABLE domains_set_domain (
                  domains_set_id INT NOT NULL, 
                  domain_name_id INT NOT NULL, 
                  PRIMARY KEY(domains_set_id, domain_name_id)
              )
              DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci 
              ENGINE = InnoDB
          ');

        $this->addSql('
            CREATE TABLE matching_context_domains_set (
                matching_context_id INT NOT NULL, 
                domains_set_id INT NOT NULL, 
                PRIMARY KEY(matching_context_id, domains_set_id)
            )
            DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci 
            ENGINE = InnoDB
        ');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE domain_name');
        $this->addSql('DROP TABLE matching_context_domain_name');
        $this->addSql('DROP TABLE domains_set');
        $this->addSql('DROP TABLE domains_set_domain');
    }
}
