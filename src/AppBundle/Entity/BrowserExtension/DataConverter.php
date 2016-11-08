<?php

namespace AppBundle\Entity\BrowserExtension;

class DataConverter
{

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

}
