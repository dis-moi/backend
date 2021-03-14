<?php

namespace App\Repository;

use App\Entity\Setting;

class SettingRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function getClassName()
    {
        return Setting::class;
    }
}
