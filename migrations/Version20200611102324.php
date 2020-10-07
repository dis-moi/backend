<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200611102324 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('alter table contributor add starred_notice int(11) null');
        $this->addSql('alter table contributor add banner_image varchar(255) null');
        $this->addSql('alter table contributor 
            add constraint starred_notice___fk 
                foreign key (starred_notice) references notice (id)
                    on delete SET NULL 
        ');
        $this->addSql('alter table notice add screenshot varchar(255) null');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE contributor DROP FOREIGN KEY starred_notice___fk');
        $this->addSql('ALTER TABLE contributor DROP starred_notice, DROP banner_image');
        $this->addSql('ALTER TABLE notice DROP screenshot');
    }
}
