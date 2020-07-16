<?php

namespace Tests\Domain\Service;

use Domain\Service\MessagePresenter;
use PHPUnit\Framework\TestCase;

const UTM_MEDIUM = 'UTM_MEDIUM';

class MessagePresenterTest extends TestCase
{
    private $messagePresenter;

    public function setUp(): void
    {
        parent::setUp();
        $this->messagePresenter = new MessagePresenter(UTM_MEDIUM);
    }

    public function test_convert_newline_to_paragraph_converts_single_line()
    {
        $content = 'My simple content';
        $this->assertEqualHtml('<p>My simple content</p>', $this->messagePresenter->present($content));
    }

    public function test_convert_newline_to_line_break_converts_regular_multi_lines()
    {
        $content = "My simple content\nWith some other content. ";
        $expectedContent = '<p>My simple content<br />With some other content. </p>';
        $this->assertEqualHtml($expectedContent, $this->messagePresenter->present($content));
    }

    public function test_convert_newline_to_line_break_works_with_crlf_multi_lines()
    {
        $content = "My simple content\r\nWith some other content. ";
        $expectedContent = '<p>My simple content<br />With some other content. </p>';
        $this->assertEqualHtml($expectedContent, $this->messagePresenter->present($content));
    }

    public function test_convert_newline_to_line_break_works_with_cr_multi_lines()
    {
        $content = "My simple content\rWith some other content. ";
        $expectedContent = '<p>My simple content<br />With some other content. </p>';
        $this->assertEqualHtml($expectedContent, $this->messagePresenter->present($content));
    }

    public function test_convert_2_new_lines_to_paragraph_converts_regular_multi_lines()
    {
        $content = "My simple content\n\nWith some other content. ";
        $expectedContent = '<p>My simple content</p><p>With some other content. </p>';
        $this->assertEqualHtml($expectedContent, $this->messagePresenter->present($content));
    }

    public function test_convert_3_new_lines_to_paragraph_converts_regular_multi_lines()
    {
        $content = "My simple content\n\n\nWith some other content. ";
        $expectedContent = '<p>My simple content</p><p>With some other content. </p>';
        $this->assertEqualHtml($expectedContent, $this->messagePresenter->present($content));
    }

    public function testItAddsTargetBlankAttributeToLink()
    {
        $this->assertEqualHtml(
            $this->messagePresenter->present('<a href="https://lmem.net">lmem</a>'),
            '<p><a href="https://lmem.net?utm_medium='.UTM_MEDIUM.'" target="_blank" rel="noopener noreferrer">lmem</a></p>'
        );
    }

    public function testItConvertsUrlToLink()
    {
        $textFullUrl = 'Un lien vers https://lmem.net/jeanmichel?ilenveut&bien=1&de=plus';
        $this->assertEqualHtml(
            '<p>Un lien vers <a href="https://lmem.net/jeanmichel?ilenveut&bien=1&de=plus&utm_medium='.UTM_MEDIUM.'" target="_blank" rel="noopener noreferrer">lmem.net/jeanmichel</a></p>',
            $this->messagePresenter->present($textFullUrl)
        );
        $textPartialUrl = 'Un lien vers www.lmem.net';
        $this->assertEqualHtml(
            '<p>Un lien vers <a href="http://www.lmem.net?utm_medium='.UTM_MEDIUM.'" target="_blank" rel="noopener noreferrer">www.lmem.net</a></p>',
            $this->messagePresenter->present($textPartialUrl)
        );
        $textPartialUrl2 = 'Un lien vers lmem.net/path';
        $this->assertEqualHtml(
            '<p>Un lien vers <a href="http://lmem.net/path?utm_medium='.UTM_MEDIUM.'" target="_blank" rel="noopener noreferrer">lmem.net/path</a></p>',
            $this->messagePresenter->present($textPartialUrl2)
        );
        $textPartialUrl3 = 'Un lien vers http://lmem.net';
        $this->assertEqualHtml(
            '<p>Un lien vers <a href="http://lmem.net?utm_medium='.UTM_MEDIUM.'" target="_blank" rel="noopener noreferrer">lmem.net</a></p>',
            $this->messagePresenter->present($textPartialUrl3)
        );
//
//        - https://www.youtube.com/watch?v=Hkom7MeY5bk
        //- http://www.driiveme.com/partenaire/villea_villeb.html?partnerId=60
        //- https://www.leboncoin.fr/recherche/?category=12&locations=Avignon&utm_source=lmem_assistant
        //- https://www.quechoisir.org/dossier-lave-linge-t387/

        $this->assertEqualHtml(
            '<p><a href="https://www.youtube.com/watch?v=Hkom7MeY5bk&utm_medium='.UTM_MEDIUM.'" target="_blank" rel="noopener noreferrer">www.youtube.com/watch</a></p>',
            $this->messagePresenter->present('https://www.youtube.com/watch?v=Hkom7MeY5bk')
        );
        $this->assertEqualHtml(
            '<p><a href="http://www.driiveme.com/partenaire/villea_villeb.html?partnerId=60&utm_medium='.UTM_MEDIUM.'" target="_blank" rel="noopener noreferrer">www.driiveme.com/partenaire/villea_villeb.html</a></p>',
            $this->messagePresenter->present('http://www.driiveme.com/partenaire/villea_villeb.html?partnerId=60')
        );
        $this->assertEqualHtml(
            '<p><a href="https://www.leboncoin.fr/recherche/?category=12&locations=Avignon&utm_source=lmem_assistant&utm_medium='.UTM_MEDIUM.'" target="_blank" rel="noopener noreferrer">www.leboncoin.fr/recherche/</a></p>',
            $this->messagePresenter->present('https://www.leboncoin.fr/recherche/?category=12&locations=Avignon&utm_source=lmem_assistant')
        );
        $this->assertEqualHtml(
            '<p><a href="https://www.quechoisir.org/dossier-lave-linge-t387/?utm_medium='.UTM_MEDIUM.'" target="_blank" rel="noopener noreferrer">www.quechoisir.org/dossier-lave-linge-t387/</a></p>',
            $this->messagePresenter->present('https://www.quechoisir.org/dossier-lave-linge-t387/')
        );
    }

    public function testItOverwritesUtmMediumIfExisting()
    {
        $textFullUrl = 'Un lien vers https://lmem.net/jeanmichel?ilenveut&utm_source=duckduckgo&utm_medium=some_medium&utm_term=search&bien=1&de=plus';
        $this->assertEqualHtml(
            '<p>Un lien vers <a href="https://lmem.net/jeanmichel?ilenveut&utm_source=duckduckgo&utm_medium='.UTM_MEDIUM.'&utm_term=search&bien=1&de=plus" target="_blank" rel="noopener noreferrer">lmem.net/jeanmichel</a></p>',
            $this->messagePresenter->present($textFullUrl)
        );
    }

    protected function assertEqualHtml($expected, $actual)
    {
        $from = ['/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s', '/> </s'];
        $to = ['>',            '<',            '\\1',      '><'];
        $this->assertEquals(
            preg_replace($from, $to, $expected),
            preg_replace($from, $to, $actual)
        );
    }
}
