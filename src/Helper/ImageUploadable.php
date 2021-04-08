<?php

declare(strict_types=1);

namespace App\Helper;

use Symfony\Component\HttpFoundation\File\File;

interface ImageUploadable
{
    public function getImageFile(): ?File;
}
