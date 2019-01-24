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

    public function testItAddUtmSourceToUrl()
    {
        $add_utm_source = DataConverter::addUtmSourceToLink("adomain.fr?query=param");
        $this->assertEquals("adomain.fr?query=param&utm_source=lmem_assistant", $add_utm_source);
    }

    public function testItAddUtmSourceToUrlWithoutQueryParam()
    {
        $add_utm_source = DataConverter::addUtmSourceToLink("http://www.adomain.fr");
        $this->assertEquals("http://www.adomain.fr?utm_source=lmem_assistant", $add_utm_source);
    }
}
