<?php

namespace AppBundle\Entity\BrowserExtension;


class MatchingContext
{
    public $recommendation_url;
    public $url_regex;

    /**
     * MatchingContext constructor.
     */
    public function __construct($recommendation_url, $url_regex)
    {
        $this->recommendation_url = $recommendation_url;
        $this->url_regex = $url_regex;
    }
}