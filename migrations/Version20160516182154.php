<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160516182154 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf('mysql' != $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('INSERT INTO fos_user (username, username_canonical, email, email_canonical, enabled, salt, password, last_login, locked, expired, expires_at, confirmation_token, password_requested_at, roles, credentials_expired, credentials_expire_at) VALUES (\'lmem\', \'lmem\', \'infra@lmem.net\', \'infra@lmem.net\', 1, \'371j7rnsxh0kwks0sck0wc40c0scc4k\', \'$2y$13$TDK/msrAF5ihzC.X6NVGyuShCK4WACjWbxOlDX4EIuqAKfDCloegW\', null, 0, 0, null, null, null, \'a:1:{i:0;s:16:"ROLE_SUPER_ADMIN";}\', 0, null);');
    }

    public function down(Schema $schema)
    {
        $this->abortIf('mysql' != $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DELETE FROM fos_user WHERE email = \'infra@lmem.net\'');
    }
}
