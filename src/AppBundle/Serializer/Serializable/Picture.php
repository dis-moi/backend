<?php


namespace AppBundle\Serializer\Serializable;


use AppBundle\Entity\Contributor;
use AppBundle\Helper\ImageUploadable;

class Picture
{
    private $uploadable;
    private $thumbs;

    private function __construct(ImageUploadable $uploadable)
    {
        $this->uploadable = $uploadable;
    }

    static function fromContributor(Contributor $contributor) : self
    {
        return new self($contributor);
    }

    public function getUploadable()
    {
        return $this->uploadable;
    }

    public function addThumb(Thumb $thumb) : self
    {
        $this->thumbs[$thumb->getName()] = $thumb;

        return $this;
    }

    public function getThumbs() : array
    {
        return $this->thumbs;
    }
}