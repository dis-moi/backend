<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210704210627 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE notice 
            ADD COLUMN badged_count int DEFAULT 0 NOT NULL,
            ADD COLUMN displayed_count int DEFAULT 0 NOT NULL,
            ADD COLUMN unfolded_count int DEFAULT 0 NOT NULL,
            ADD COLUMN clicked_count int DEFAULT 0 NOT NULL,
            ADD COLUMN liked_count int DEFAULT 0 NOT NULL,
            ADD COLUMN disliked_count int DEFAULT 0 NOT NULL,
            ADD COLUMN dismissed_count int DEFAULT 0 NOT NULL;
        ');

        $this->addSql('CREATE INDEX rating_notice_type_index ON rating (notice_id, type);');

        $this->addSql('UPDATE notice n
            SET n.badged_count = (SELECT COUNT(*) FROM rating r WHERE r.notice_id = n.id AND r.type = \'badge\'),
                n.displayed_count = (SELECT COUNT(*) FROM rating r WHERE r.notice_id = n.id AND r.type = \'display\'),
                n.unfolded_count = (SELECT COUNT(*) FROM rating r WHERE r.notice_id = n.id AND r.type = \'unfold\'),
                n.clicked_count = (SELECT COUNT(*) FROM rating r WHERE r.notice_id = n.id AND r.type = \'outbound-click\'),
                n.liked_count = (SELECT COUNT(*) FROM rating r WHERE r.notice_id = n.id AND r.type = \'like\') - (SELECT COUNT(*) FROM rating r WHERE r.notice_id = n.id AND r.type = \'unlike\'),
                n.disliked_count = (SELECT COUNT(*) FROM rating r WHERE r.notice_id = n.id AND r.type = \'dislike\') - (SELECT COUNT(*) FROM rating r WHERE r.notice_id = n.id AND r.type = \'undislike\'),
                n.dismissed_count = (SELECT COUNT(*) FROM rating r WHERE r.notice_id = n.id AND r.type = \'dismiss\') - (SELECT COUNT(*) FROM rating r WHERE r.notice_id = n.id AND r.type = \'undismiss\');
                ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE rating DROP INDEX rating_notice_type_index');

        $this->addSql('ALTER TABLE notice 
            DROP COLUMN badged_count,
            DROP COLUMN displayed_count,
            DROP COLUMN unfolded_count,
            DROP COLUMN clicked_count,
            DROP COLUMN liked_count,
            DROP COLUMN disliked_count,
            DROP COLUMN dismissed_count
        ');
    }
}
