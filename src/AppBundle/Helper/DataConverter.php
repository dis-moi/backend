<?php

namespace AppBundle\Helper;

use Youthweb\UrlLinker\UrlLinker;

class DataConverter
{
    static public function convertFullMessage(string $message) : string
    {
        $message = self::convertNewLinesToParagraphs($message);
        $message = self::addTargetBlankToLinks($message);
        $message = self::addLinksToUrls($message);

        return substr($message, 0, 500);
    }

    static public function convertNewLinesToParagraphs(string $content) : string
    {
        $content = str_replace("\r\n", "\n", $content);
        $content = str_replace("\r", "\n", $content);
        $pattern = "/^(.*)$/m";
        return preg_replace($pattern, '<p>$1</p>', $content);
    }

    static public function addTargetBlankToLinks(string $message) : string
    {
        return str_replace('<a ', '<a target="_blank" rel="noopener noreferrer" ', $message);
    }

    static public function addLinksToUrls(string $message) : string
    {
        $urlLinker = new UrlLinker([
            // Do not format emails...
            'emailLinkCreator' => function($email) { return $email; },
        ]);
        return $urlLinker->linkUrlsInTrustedHtml($message);
    }
}
