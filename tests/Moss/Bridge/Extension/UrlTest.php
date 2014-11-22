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


class UrlTest extends \PHPUnit_Framework_TestCase
{
    public function testName()
    {
        $router = $this->getMock('\Moss\Http\Router\RouterInterface');

        $url = new Url($router);

        $this->assertEquals('Url', $url->getName());
    }

    public function testFunctions()
    {
        $router = $this->getMock('\Moss\Http\Router\RouterInterface');

        $url = new Url($router);
        $result = $url->getFunctions();

        $expected = array(
            'url' => new \Twig_Function_Method($url, 'url'),
        );

        $this->assertEquals($expected, $result);
    }

    public function testUrl()
    {
        $router = $this->getMock('\Moss\Http\Router\RouterInterface');
        $router->expects($this->once())
            ->method('make')
            ->with('foo:bar', array('foo' => 'bar'));

        $url = new Url($router);
        $url->url('foo:bar', array('foo' => 'bar'));
    }
}
