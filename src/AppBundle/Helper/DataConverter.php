<?php

namespace AppBundle\Helper;

use League\Uri\Http;
use League\Uri\Modifiers\MergeQuery;

class DataConverter
{
    static protected $hrefAddition = 'utm_source=lmem_assistant';

    /**
     * @param string $message
     * @return string
     */
    static public function convertFullMessage($message)
    {
        $message = self::convertNewLinesToParagraphs($message);
        $message = self::addTargetBlankToLinks($message);
        $message = self::addUtmSourceToLinks($message);

        return substr($message, 0, 500);
    }

    /**
     * @param string $content
     * @return string
     */
    static public function convertNewLinesToParagraphs($content)
    {
        $content = str_replace("\r\n", "\n", $content);
        $content = str_replace("\r", "\n", $content);
        $pattern = "/^(.*)$/m";
        return preg_replace($pattern, '<p>$1</p>', $content);
    }

    /**
     * @param string $message
     * @return string
     */
    static public function addTargetBlankToLinks($message)
    {
        return str_replace('<a ', '<a target="_blank" rel="noopener noreferrer" ', $message);
    }

    /**
     * @param string $message
     * @return string
     */
    static public function addUtmSourceToLinks($message)
    {
        $pattern = '/href="(.*)"/';
        if(preg_match_all($pattern, $message, $urls)) {
            foreach ($urls[1] as $url) {
                $message = str_replace($url,
                    static::addUtmSourceToLink($url),
                    $message
                );
            }
        }

        return $message;
    }

    /**
     * @param string $href
     * @return string
     */
    static public function addUtmSourceToLink($href)
    {
        return (new MergeQuery(static::$hrefAddition))->process(Http::createFromString($href))->__toString();
    }
}
