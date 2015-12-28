<?php
/**
 * Url Class tests
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @version 3.0
 * @date December 25th, 2015
 */

namespace Nova\Tests\Net;

/**
 * Class UrlTest
 * @package Nova\Tests\Net
 * @covers \Nova\Net\Url
 */
class UrlTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Spoof the $_SERVER variables for router.
     *
     * @param string $url part of the url (request)
     * @param string $method method, GET, POST, etc
     * @param string $script script, mostly index.php
     */
    private function spoofRouter($url, $method = 'GET', $script = 'index.php')
    {
        $_SERVER['REQUEST_URI'] = $url;
        $_SERVER['SCRIPT_NAME'] = $script;
        $_SERVER['REQUEST_METHOD'] = $method;
    }




    public function testSafeSlug()
    {
        $input_1 = 'Sample Input costs 5$!';
        $expected_1 = "sample-input-costs-5";
        $input_2 = 'Simple Fr@mew0r!K Input costs 5$!';
        $expected_2 = "simple-fr-mew0r-k-input-costs-5";
        $input_3 = 'œµSimple';
        $expected_3 = "oeusimple";
        $input_4 = 'Example of a very long sentence, which contains some spaces!';
        $expected_4 = "example-of-a-very-long-sentence-which-contains-some-spaces";


        $current_1 = \Nova\Net\Url::generateSafeSlug($input_1);
        $current_2 = \Nova\Net\Url::generateSafeSlug($input_2);
        $current_3 = \Nova\Net\Url::generateSafeSlug($input_3);
        $current_4 = \Nova\Net\Url::generateSafeSlug($input_4);

        $this->assertEquals($expected_1, $current_1);
        $this->assertEquals($expected_2, $current_2);
        $this->assertEquals($expected_3, $current_3);
        $this->assertEquals($expected_4, $current_4);
    }


    /**
     * @covers \Nova\Net\Url::segments
     * @covers \Nova\Net\Url::segment
     * @covers \Nova\Net\Url::getSegment
     * @covers \Nova\Net\Url::lastSegment
     * @covers \Nova\Net\Url::firstSegment
     * @covers \Nova\Net\Url::detectUri
     */
    public function testSegments()
    {
        $this->spoofRouter('/test/very/long/url/with/MANY/weird/$hars');
        $expected = array('test', 'very', 'long', 'url', 'with', 'MANY', 'weird', '$hars');
        $segments = \Nova\Net\Url::segments();

        $this->assertEquals($expected, $segments);

        $seg_1 = \Nova\Net\Url::segment(1);
        $this->assertEquals('very', $seg_1);
        $seg_2 = \Nova\Net\Url::segment(2);
        $this->assertEquals('long', $seg_2);

        $last = \Nova\Net\Url::lastSegment($segments);
        $this->assertEquals('$hars', $last);

        $first = \Nova\Net\Url::firstSegment($segments);
        $this->assertEquals('test', $first);

        $null = \Nova\Net\Url::getSegment($segments, 55);
        $this->assertNull($null);
    }


    /**
     * @covers \Nova\Net\Url::autoLink
     */
    public function testAutoLink()
    {
        $input = "Welcome to this awesome test, info on http://www.google.com or on https://website.com/. You can also watch our videos on https://youtu.be.";
        $expected_1 = "Welcome to this awesome test, info on <a href=\"http://www.google.com\">http://www.google.com</a> or on <a href=\"https://website.com/\">https://website.com/</a>. You can also watch our videos on <a href=\"https://youtu.be\">https://youtu.be</a>.";
        $output_1 = \Nova\Net\Url::autoLink($input);

        $expected_2 = "Welcome to this awesome test, info on <a href=\"http://www.google.com\">Follow Link</a> or on <a href=\"https://website.com/\">Follow Link</a>. You can also watch our videos on <a href=\"https://youtu.be\">Follow Link</a>.";
        $output_2 = \Nova\Net\Url::autoLink($input, "Follow Link");

        $this->assertEquals($expected_1, $output_1);
        $this->assertEquals($expected_2, $output_2);
    }



    /**
     * @covers \Nova\Net\Url::relativeTemplatePath
     * @covers \Nova\Net\Url::templatePath
     */
    public function testTemplatePaths()
    {
        $expected_1 = "/templates/default/assets/";
        $output_1 = \Nova\Net\Url::templatePath();

        $this->assertEquals($expected_1, $output_1);

        $expected_2 = "/templates/MyTestTemplate/assets/";
        $output_2 = \Nova\Net\Url::templatePath('MyTestTemplate');

        $this->assertEquals($expected_2, $output_2);


        $expected_3 = "templates/default/assets/";
        $output_3 = \Nova\Net\Url::relativeTemplatePath();

        $this->assertEquals($expected_3, $output_3);

        $expected_4 = "templates/MyTestTemplate/assets/";
        $output_4 = \Nova\Net\Url::relativeTemplatePath('MyTestTemplate');

        $this->assertEquals($expected_4, $output_4);
    }
}