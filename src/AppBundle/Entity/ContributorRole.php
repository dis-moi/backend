<?php

namespace AppBundle\Entity;

use MabeEnum\Enum;

class ContributorRole extends Enum
{
    const SUPER_ADMIN_ROLE = 'super_admin';
    const EDITOR_ROLE = 'editor';
    const AUTHOR_ROLE = 'author';

    static function getDefault()
    {
        return self::AUTHOR_ROLE();
    }

    public function __toString()
    {
        return self::getValue();
    }
}
