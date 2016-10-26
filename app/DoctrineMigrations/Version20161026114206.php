<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161026114206 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needsv
        $this->addSql('CREATE TABLE resource_copy LIKE resource');
        $this->addSql('INSERT resource_copy SELECT * FROM resource');
        $this->addSql('CREATE TABLE alternative_copy LIKE alternative');
        $this->addSql('INSERT alternative_copy SELECT * FROM alternative');
        $this->addSql('DELETE FROM resource;');
        $this->addSql('INSERT INTO resource(recommendation_id, label, url) SELECT recommendation_id, label, urlToRedirect FROM alternative ;');
        $this->addSql('DELETE FROM alternative ;');
        $this->addSql('INSERT INTO alternative(recommendation_id, label, urlToRedirect) SELECT recommendation_id, label, url  FROM resource_copy;');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('DELETE FROM resource');
        $this->addSql('INSERT resource SELECT * FROM resource_copy');
        $this->addSql('DELETE FROM alternative');
        $this->addSql('INSERT alternative SELECT * FROM alternative_copy');

    }
}
