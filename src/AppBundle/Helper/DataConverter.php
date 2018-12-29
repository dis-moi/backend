<?php

namespace AppBundle\Helper;

use League\Uri\Http;
use League\Uri\Modifiers\MergeQuery;

class DataConverter
{
    /**
     * @param string $message
     * @return string
     */
    public static function convertFullMessage($message)
    {
        $message = self::convertNewLinesToParagraphs($message);
        $message = self::addTargetBlankToLinks($message);
        $message = self::addUtmSourceToLinks($message);

        return $message;
    }

    /**
     * @param $content
     *
     * @return string
     */
    public static function convertNewLinesToParagraphs($content)
    {
        $content = str_replace("\r\n", "\n", $content);
        $content = str_replace("\r", "\n", $content);
        $pattern = "/^(.*)$/m";
        return preg_replace($pattern, '<p>$1</p>', $content);
    }

    static public function addTargetBlankToLinks($message)
    {
        return str_replace('<a ', '<a target="_blank" rel="noopener noreferrer" ', $message);
    }

    static public function addUtmSourceToLinks($message)
    {
        $addition = 'utm_source=lmem_assistant';

        $pattern = '/href="(.*)"/';
        if(preg_match_all($pattern, $message, $urls)) {
            foreach ($urls as $url) {
                str_replace($url,
                    (new MergeQuery($addition))->process(Http::createFromString($url))->__toString(),
                    $message
                );
            }
        }

        return $message;
    }

}
