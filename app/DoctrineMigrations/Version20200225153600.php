<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200225153600 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            UPDATE notice n
            SET n.message = IF(
                (SELECT COUNT(*) FROM source s WHERE s.notice_id = n.id) = 0,
                n.message,
                CONCAT(
                    n.message,
                    \'\n<strong>\',
                    (SELECT s.label FROM source s WHERE s.notice_id = n.id),
                    \' : </strong>\',
                    (SELECT s.url FROM source s WHERE s.notice_id = n.id)
                )
            )
        ');
        $this->addSql('DROP TABLE `source`;');
        $this->addSql('DROP TABLE `notice_channel`;');
        $this->addSql('DROP TABLE `channel`;');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            create table source
                (
                    id        int auto_increment primary key,
                    notice_id int          null,
                    label     varchar(255) null,
                    url       varchar(255) null,
                    constraint UNIQ_5F8A7F737D540AB unique (notice_id),
                    constraint FK_5F8A7F737D540AB foreign key (notice_id) references notice (id)
                )
                collate = utf8_unicode_ci
            ;');

        $this->addSql('
            create table channel
                (
                    id int auto_increment primary key,
                    name varchar(255) not null
                )
                collate = utf8_unicode_ci;
            ');

        $this->addSql('
            create table notice_channel
                (
                    notice_id  int not null,
                    channel_id int not null,
                    primary key (notice_id, channel_id),
                    constraint FK_2C1C75F87D540AB foreign key (notice_id) references notice (id) on delete cascade,
                    constraint FK_FF9A87EB72F5A1AA foreign key (channel_id) references channel (id) on delete cascade
                )
                collate = utf8_unicode_ci
            ;');

        $this->addSql('create index IDX_2C1C75F872F5A1AA on notice_channel (channel_id);');

        $this->addSql('create index IDX_2C1C75F87D540AB on notice_channel (notice_id);');
    }
}
