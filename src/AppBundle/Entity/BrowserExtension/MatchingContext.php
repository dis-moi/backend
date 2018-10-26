<?php

namespace AppBundle\Entity\BrowserExtension;


class MatchingContext
{
    public $recommendation_url;
    public $url_regex;
    public $exclude_url_regex;

    /**
     * MatchingContext constructor.
     */
    public function __construct($recommendation_url, $url_regex, $exclude_url_regex)
    {
        $this->recommendation_url = $recommendation_url;
        $this->url_regex = $url_regex;
        $this->exclude_url_regex = $exclude_url_regex;
    }
}
