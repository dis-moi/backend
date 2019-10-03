<?php

namespace AppBundle\Helper;

use Youthweb\UrlLinker\UrlLinker;

class DataConverter
{
    static public function convertFullMessage(string $message) : string
    {
        $message = self::truncate($message, 500);
        $message = self::convertNewLinesToParagraphs($message);
        $message = self::addLinksToUrls($message);
        $message = self::addTargetBlankToLinks($message);

        return $message;
    }

    static public function convertFullIntro(string $intro) : string
    {
        $intro = self::convertNewLinesToParagraphs($intro);
        $intro = self::addLinksToUrls($intro);
        $intro = self::addTargetBlankToLinks($intro);

        return $intro;
    }

    static public function truncate(string $message, $length) : string
    {
        return mb_strlen($message) > $length ? mb_strcut($message, 0, $length) . '...' : $message;
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
