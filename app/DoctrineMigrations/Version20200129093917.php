<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Dans les champs message et source (s'il existe encore à ce moment), remplacer "choisir.lmem.net" par "lmem.net".
 */
final class Version20200129093917 extends AbstractMigration
{
    const BEFORE = 'choisir.lmem.net';
    const AFTER = 'lmem.net';

    public function up(Schema $schema): void
    {
        $this->addSql("
        UPDATE notice
        SET message = REPLACE(message, '".self::BEFORE."', '".self::AFTER."'),
            note = REPLACE(note, '".self::BEFORE."', '".self::AFTER."')
        WHERE INSTR(message, '".self::BEFORE."') > 0
           OR INSTR(note, '".self::BEFORE."') > 0; 
      ");
        $this->addSql("
        UPDATE source
        SET label = REPLACE(label, '".self::BEFORE."', '".self::AFTER."'),
            url = REPLACE(url, '".self::BEFORE."', '".self::AFTER."')
        WHERE INSTR(label, '".self::BEFORE."') > 0
           OR INSTR(url, '".self::BEFORE."') > 0; 
      ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("
        UPDATE notice
        SET message = REPLACE(message, '".self::AFTER."', '".self::BEFORE."'),
            note = REPLACE(note, '".self::AFTER."', '".self::BEFORE."')
        WHERE INSTR(message, '".self::AFTER."') > 0
           OR INSTR(note, '".self::AFTER."') > 0; 
      ");
        $this->addSql("
        UPDATE source
        SET label = REPLACE(label, '".self::AFTER."', '".self::BEFORE."'),
            url = REPLACE(url, '".self::AFTER."', '".self::BEFORE."')
        WHERE INSTR(label, '".self::AFTER."') > 0
           OR INSTR(url, '".self::AFTER."') > 0; 
      ");
    }
}
