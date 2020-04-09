<?php

namespace AppBundle\Helper;

use Youthweb\UrlLinker\UrlLinker;

class DataConverter
{
    public static function convertFullMessage(string $message): string
    {
        $message = self::convertNewLinesToParagraphs($message);
        $message = self::addLinksToUrls($message);
        $message = self::addTargetBlankToLinks($message);

        return $message;
    }

    public static function convertFullIntro(string $intro): string
    {
        $intro = self::convertNewLinesToParagraphs($intro);
        $intro = self::addLinksToUrls($intro);
        $intro = self::addTargetBlankToLinks($intro);

        return $intro;
    }

    public static function convertNewLinesToParagraphs(string $content): string
    {
        $content = str_replace("\r\n", "\n", $content);
        $content = str_replace("\r", "\n", $content);
        $pattern = '/^(.*)$/m';

        return preg_replace($pattern, '<p>$1</p>', $content);
    }

    public static function addTargetBlankToLinks(string $message): string
    {
        return str_replace('<a ', '<a target="_blank" rel="noopener noreferrer" ', $message);
    }

    public static function addLinksToUrls(string $message): string
    {
        $urlLinker = new UrlLinker([
            // Do not format emails...
            'emailLinkCreator' => function ($email) { return $email; },
        ]);

        return $urlLinker->linkUrlsInTrustedHtml($message);
    }
}
