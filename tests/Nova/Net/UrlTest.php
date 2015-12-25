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
}