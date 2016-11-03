<?php

use AppBundle\Entity\BrowserExtension\DataConverter;

class DataConverterTest extends PHPUnit_Framework_TestCase
{

    public function setup()
    {
    }

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
}
