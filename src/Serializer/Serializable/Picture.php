<?php

namespace App\Serializer\Serializable;

use App\Entity\Contributor;
use App\Helper\ImageUploadable;

class Picture
{
    private $uploadable;
    private $thumbs;

    private function __construct(ImageUploadable $uploadable)
    {
        $this->uploadable = $uploadable;
    }

    public static function fromContributor(Contributor $contributor): self
    {
        return new self($contributor);
    }

    public function getUploadable()
    {
        return $this->uploadable;
    }

    public function addThumb(Thumb $thumb): self
    {
        $this->thumbs[$thumb->getName()] = $thumb;

        return $this;
    }

    public function getThumbs(): array
    {
        return $this->thumbs;
    }
}
