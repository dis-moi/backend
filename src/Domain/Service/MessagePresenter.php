<?php

namespace Domain\Service;

use League\Uri\Uri;
use League\Uri\UriModifier;
use function Sentry\captureException;
use Throwable;
use Youthweb\UrlLinker\UrlLinker;

class MessagePresenter
{
    /**
     * @var string
     */
    private $utmMedium;

    /**
     * MessagePresenter constructor.
     */
    public function __construct(string $utmMedium)
    {
        $this->utmMedium = $utmMedium;
    }

    public function present(string $message): string
    {
        $message = $this->convertNewLinesToParagraphs($message);
        $message = $this->addLinksToUrls($message);
        $message = $this->addTargetBlankAndUtmToLinks($message);

        return $message;
    }

    public function strip(string $message): string
    {
        $message = strip_tags($message);
        $message = $this->convertNewLinesToParagraphs($message);

        return $message;
    }

    private function convertNewLinesToParagraphs(string $content): string
    {
        return preg_replace('/(?:<br[^>]*\/>\s*){2,}/', '</p><p>', '<p>'.nl2br($content).'</p>');
    }

    private function addTargetBlankAndUtmToLinks(string $message): string
    {
        return preg_replace_callback(
            '/<a ([^>]*)href="([^"]+)"([^>]*)>([^<]+)<\/a>/',
            function ($matches) {
                return '<a '.$matches[1].' '.$matches[3].' href="'.$this->setUtmForUrl($matches[2]).'" target="_blank" rel="noopener noreferrer">'.$matches[4].'</a>';
            },
            $message
        );
    }

    private function addLinksToUrls(string $message): string
    {
        $urlLinker = new UrlLinker([
            'htmlLinkCreator' => function ($url, $content) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    $url,
                    $this->escapeHtml($content)
                );
            },
            // Do not format emails...
            'emailLinkCreator' => function ($email) { return $email; },
        ]);

        return $urlLinker->linkUrlsInTrustedHtml($message);
    }

    /**
     * @param $url
     *
     * @return escaped $url
     */
    private function escapeHtml($url)
    {
        $flags = ENT_COMPAT | ENT_HTML401;
        $encoding = ini_get('default_charset');
        $double_encode = false; // Do not double encode

        return htmlspecialchars($url, $flags, $encoding, $double_encode);
    }

    private function setUtmForUrl($url)
    {
        try {
            return UriModifier::mergeQuery(
                Uri::createFromString(trim($url)),
                "utm_medium=$this->utmMedium"
            );
        } catch (Throwable $e) {
            captureException($e);
        }
    }
}
