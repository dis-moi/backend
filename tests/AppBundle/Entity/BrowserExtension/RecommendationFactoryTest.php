<?php
/**
 * Created by PhpStorm.
 * User: alemaire
 * Date: 08/07/2017
 * Time: 20:46
 */

namespace AppBundle\Entity\BrowserExtension;


class RecommendationFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testItAddUtmSourceToUrl()
    {
        $add_utm_source = RecommendationFactory::add_utm_source("adomain.fr?query=param");
        $this->assertEquals("adomain.fr?query=param&utm_source=lmem_assistant", $add_utm_source);
    }

    public function testItAddUtmSourceToUrlWithoutQueryParam()
    {
        $add_utm_source = RecommendationFactory::add_utm_source("http://www.adomain.fr");
        $this->assertEquals("http://www.adomain.fr?utm_source=lmem_assistant", $add_utm_source);
    }
}
