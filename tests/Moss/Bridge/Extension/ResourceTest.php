<?php

/*
* This file is part of the moss-twig-bridge package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Moss\Bridge\Extension;

use Moss\Bridge\TokenParser\Resource as TokenParserResource;

class ResourceTest extends \PHPUnit_Framework_TestCase
{
    public function testName()
    {
        $resource = new Resource();

        $this->assertEquals('resource', $resource->getName());
    }

    public function testTokenParsers()
    {
        $resource = new Resource();

        $result = $resource->getTokenParsers();

        $expected = array(new TokenParserResource());

        $this->assertEquals($expected, $result);
    }
}
