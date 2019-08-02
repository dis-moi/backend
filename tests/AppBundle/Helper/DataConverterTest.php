<?php
namespace Tests\AppBundle\Helper;

use AppBundle\Helper\DataConverter;
use PHPUnit\Framework\TestCase;

class DataConverterTest extends TestCase
{
    public function test_convert_newline_to_paragraph_converts_single_line()
    {
        $content = "My simple content";
        $this->assertEquals("<p>My simple content</p>", DataConverter::convertNewLinesToParagraphs($content));
    }

    public function test_convert_newline_to_paragraph_converts_regular_multi_lines()
    {
        $content = "My simple content\nWith some other content. ";
        $expectedContent = "<p>My simple content</p>\n<p>With some other content. </p>";
        $this->assertEquals($expectedContent, DataConverter::convertNewLinesToParagraphs($content));
    }

    public function test_convert_newline_to_paragraph_works_with_crlf_multi_lines()
    {
        $content = "My simple content\r\nWith some other content. ";
        $expectedContent = "<p>My simple content</p>\n<p>With some other content. </p>";
        $this->assertEquals($expectedContent, DataConverter::convertNewLinesToParagraphs($content));
    }

    public function test_convert_newline_to_paragraph_works_with_cr_multi_lines()
    {
        $content = "My simple content\rWith some other content. ";
        $expectedContent = "<p>My simple content</p>\n<p>With some other content. </p>";
        $this->assertEquals($expectedContent, DataConverter::convertNewLinesToParagraphs($content));
    }

    public function testItTruncatesMessage()
    {
        $truncateAt = 100;
        $suffix = '...';

        $shortText = "This sentence is 63 characters long, which is shorter than 100.";
        $longText = "$shortText $shortText";

        $unmodified = DataConverter::truncate($shortText, $truncateAt);
        $truncated = DataConverter::truncate($longText, $truncateAt);

        $this->assertEquals($shortText, $unmodified);
        $this->assertStringEndsWith($suffix, $truncated);
        $this->assertEquals($truncateAt + mb_strlen($suffix), mb_strlen($truncated));
    }

    public function testItAddsTargetBlankAttributeToLink()
    {
        $link = DataConverter::addTargetBlankToLinks('<a href="https://lmem.net">lmem</a>');
        $this->assertEquals('<a target="_blank" rel="noopener noreferrer" href="https://lmem.net">lmem</a>', $link);
    }

    public function testItConvertsUrlToLink()
    {
        $textFullUrl = 'Un lien vers https://lmem.net/jeanmichel?ilenveut&bien=1&de=plus';
        $this->assertEquals(
            'Un lien vers <a href="https://lmem.net/jeanmichel?ilenveut&amp;bien=1&amp;de=plus">lmem.net/jeanmichel</a>',
            DataConverter::addLinksToUrls($textFullUrl)
        );
        $textPartialUrl = 'Un lien vers www.lmem.net';
        $this->assertEquals(
            'Un lien vers <a href="http://www.lmem.net">www.lmem.net</a>',
            DataConverter::addLinksToUrls($textPartialUrl)
        );
        $textPartialUrl2 = 'Un lien vers lmem.net/path';
        $this->assertEquals(
            'Un lien vers <a href="http://lmem.net/path">lmem.net/path</a>',
            DataConverter::addLinksToUrls($textPartialUrl2)
        );
        $textPartialUrl3 = 'Un lien vers http://lmem.net';
        $this->assertEquals(
            'Un lien vers <a href="http://lmem.net">lmem.net</a>',
            DataConverter::addLinksToUrls($textPartialUrl3)
        );
        $htmlWithLink = '<a href="https://www.lmem.net">does not convert</a>';
        $this->assertEquals(
            $htmlWithLink,
            DataConverter::addLinksToUrls($htmlWithLink)
        );
    }
}
