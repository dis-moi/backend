<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160516181824 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' != $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE alternative (id INT AUTO_INCREMENT NOT NULL, recommendation_id INT DEFAULT NULL, urlToRedirect VARCHAR(255) NOT NULL, `label` VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_EFF5DFAD173940B (recommendation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE filter (id INT AUTO_INCREMENT NOT NULL, `label` VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE matching_context (id INT AUTO_INCREMENT NOT NULL, recommendation_id INT DEFAULT NULL, urlRegex VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_665341C1D173940B (recommendation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recommendation (id INT AUTO_INCREMENT NOT NULL, source_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_433224D2953C1C61 (source_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recommendation_filter (recommendation_id INT NOT NULL, filter_id INT NOT NULL, INDEX IDX_74D6F1E7D173940B (recommendation_id), INDEX IDX_74D6F1E7D395B25E (filter_id), PRIMARY KEY(recommendation_id, filter_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE source (id INT AUTO_INCREMENT NOT NULL, recommendation_id INT DEFAULT NULL, `label` VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, url VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_5F8A7F73D173940B (recommendation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fos_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, username_canonical VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, locked TINYINT(1) NOT NULL, expired TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', credentials_expired TINYINT(1) NOT NULL, credentials_expire_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_957A647992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_957A6479A0D96FBF (email_canonical), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE alternative ADD CONSTRAINT FK_EFF5DFAD173940B FOREIGN KEY (recommendation_id) REFERENCES recommendation (id)');
        $this->addSql('ALTER TABLE matching_context ADD CONSTRAINT FK_665341C1D173940B FOREIGN KEY (recommendation_id) REFERENCES recommendation (id)');
        $this->addSql('ALTER TABLE recommendation ADD CONSTRAINT FK_433224D2953C1C61 FOREIGN KEY (source_id) REFERENCES source (id)');
        $this->addSql('ALTER TABLE recommendation_filter ADD CONSTRAINT FK_74D6F1E7D173940B FOREIGN KEY (recommendation_id) REFERENCES recommendation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recommendation_filter ADD CONSTRAINT FK_74D6F1E7D395B25E FOREIGN KEY (filter_id) REFERENCES filter (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE source ADD CONSTRAINT FK_5F8A7F73D173940B FOREIGN KEY (recommendation_id) REFERENCES recommendation (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' != $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE recommendation_filter DROP FOREIGN KEY FK_74D6F1E7D395B25E');
        $this->addSql('ALTER TABLE alternative DROP FOREIGN KEY FK_EFF5DFAD173940B');
        $this->addSql('ALTER TABLE matching_context DROP FOREIGN KEY FK_665341C1D173940B');
        $this->addSql('ALTER TABLE recommendation_filter DROP FOREIGN KEY FK_74D6F1E7D173940B');
        $this->addSql('ALTER TABLE source DROP FOREIGN KEY FK_5F8A7F73D173940B');
        $this->addSql('ALTER TABLE recommendation DROP FOREIGN KEY FK_433224D2953C1C61');
        $this->addSql('DROP TABLE alternative');
        $this->addSql('DROP TABLE filter');
        $this->addSql('DROP TABLE matching_context');
        $this->addSql('DROP TABLE recommendation');
        $this->addSql('DROP TABLE recommendation_filter');
        $this->addSql('DROP TABLE source');
        $this->addSql('DROP TABLE fos_user');
    }
}
