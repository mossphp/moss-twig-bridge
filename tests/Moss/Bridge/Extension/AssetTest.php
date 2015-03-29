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


class AssetTest extends \PHPUnit_Framework_TestCase
{
    public function testName()
    {
        $router = $this->getMock('\Moss\Http\Router\RouterInterface');

        $asset = new Asset($router);

        $this->assertEquals('asset', $asset->getName());
    }

    public function testFunctions()
    {
        $asset = new Asset('./{asset}');
        $result = $asset->getFunctions();

        $this->assertArrayHasKey('asset', $result);
        $this->assertInstanceOf('\Twig_SimpleFunction', $result['asset']);
    }

    public function testAsset()
    {
        $asset = new Asset('./{asset}');
        $result = $asset->make('/css/style.css');

        $this->assertEquals('./css/style.css', $result);
    }

    public function testInTwig()
    {
        $options = [
            'debug' => true,
            'auto_reload' => true,
            'strict_variables' => false,
            'cache' => false,
        ];

        $loader = new \Twig_Loader_Array(['index.html' => '{{ asset(\'/css/style.css\') }}']);

        $twig = new \Twig_Environment($loader, $options);
        $twig->setExtensions([ new Asset('./{asset}') ]);

        $result = $twig->render('index.html');

        $this->assertEquals('./css/style.css', $result);
    }
}
