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

        $this->assertEquals('url', $url->getName());
    }

    public function testFunctions()
    {
        $router = $this->getMock('\Moss\Http\Router\RouterInterface');

        $url = new Url($router);
        $result = $url->getFunctions();

        $this->assertArrayHasKey('url', $result);
        $this->assertInstanceOf('\Twig_SimpleFunction', $result['url']);
    }

    public function testUrl()
    {
        $router = $this->getMock('\Moss\Http\Router\RouterInterface');
        $router->expects($this->once())
            ->method('make')
            ->with('foo:bar', ['foo' => 'bar']);

        $url = new Url($router);
        $url->make('foo:bar', ['foo' => 'bar']);
    }

    public function testInTwig()
    {
        $router = $this->getMock('\Moss\Http\Router\RouterInterface');
        $router->expects($this->any())
            ->method('make')
            ->will($this->returnValue('http://test.com/foobar/?foo=bar'));

        $options = [
            'debug' => true,
            'auto_reload' => true,
            'strict_variables' => false,
            'cache' => false,
        ];

        $loader = new \Twig_Loader_Array(['index.html' => '{{ url(\'foo:bar\', {\'foo\': \'bar\'}) }}']);

        $twig = new \Twig_Environment($loader, $options);
        $twig->setExtensions([ new Url($router) ]);

        $result = $twig->render('index.html');

        $this->assertEquals('http://test.com/foobar/?foo=bar', $result);
    }
}
