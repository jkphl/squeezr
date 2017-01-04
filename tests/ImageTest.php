<?php

/**
 * squeezr
 *
 * @category    Tollwerk
 * @package     Tollwerk\Squeezr
 * @subpackage  ${NAMESPACE}
 * @author      Joschi Kuphal <joschi@tollwerk.de> / @jkphl
 * @copyright   Copyright © 2017 Joschi Kuphal <joschi@tollwerk.de> / @jkphl
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

/***********************************************************************************
 *  The MIT License (MIT)
 *
 *  Copyright © 2017 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy of
 *  this software and associated documentation files (the "Software"), to deal in
 *  the Software without restriction, including without limitation the rights to
 *  use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 *  the Software, and to permit persons to whom the Software is furnished to do so,
 *  subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 *  FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 *  COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 *  IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 *  CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 ***********************************************************************************/

use Tollwerk\Squeezr\Image;

/**
 * Image engine tests
 *
 * @package Tollwerk\Squeezr
 */
class ImageTest extends PHPUnit_Framework_TestCase
{
    /**
     * This method is called before the first test of this test class is run.
     *
     * @since Method available since Release 3.4.0
     */
    public static function setUpBeforeClass()
    {
        $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__);
        require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'squeezr'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'image.php';
    }

    /**
     * Test the instantiation of the image engine
     */
    public function testInstantiation()
    {
        $image = Image::instance('tests/fixture/test.jpg');
        $this->assertInstanceOf('Tollwerk\\Squeezr\\Image', $image);
    }
}
