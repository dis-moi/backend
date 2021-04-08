<?php

declare(strict_types=1);

namespace App\Serializer\Serializable;

use App\Entity\Contributor;
use App\Helper\ImageUploadable;

class Picture
{
    /**
     * @var ImageUploadable
     */
    private $uploadable;

    /**
     * @var Thumb[]
     */
    private $thumbs;

    private function __construct(ImageUploadable $uploadable)
    {
        $this->uploadable = $uploadable;
    }

    public static function fromContributor(Contributor $contributor): self
    {
        return new self($contributor);
    }

    public function getUploadable(): ImageUploadable
    {
        return $this->uploadable;
    }

    public function addThumb(Thumb $thumb): self
    {
        $this->thumbs[$thumb->getName()] = $thumb;

        return $this;
    }

    /**
     * @return Thumb[]
     */
    public function getThumbs(): array
    {
        return $this->thumbs;
    }
}
