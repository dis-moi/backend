<?php

namespace AppBundle\Entity\BrowserExtension;

class Criterion
{

    /** @var string */
    public $id;

    /** @var string */
    public $label;

    /** @var string */
    public $slug;

    /**
     * Criterion constructor.
     *
     * @param $id
     * @param $label
     * @param $slug
     */
    public function __construct($id, $label, $slug)
    {
        $this->id  = $id;
        $this->label = $label;
        $this->slug = $slug;
    }
}
