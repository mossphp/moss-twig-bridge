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
    private static $path;

    private $resource;
    private $public;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$path = __DIR__ . '/'.self::rand(8);
    }

    public function setUp()
    {
        $this->tearDown();

        $this->resource = self::$path.'/resource/{bundle}/';
        $this->public = self::$path.'/public/{bundle}/';

        $path = strtr($this->resource, ['{bundle}' => 'test']);
        if(!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        file_put_contents($path . 'style.css', '/* nothing here */');
    }

    protected static function rand($len, $chars = '1234567890qwertyuioplkjhgfdsazxcvbnm')
    {
        $str = '';
        $max = strlen($chars)-1;
        while($len--) {
            $i = rand(0, $max);
            $str .= $chars[$i];
        }

        return $str;
    }

    public function tearDown()
    {
        if(!file_exists(self::$path)) {
            return;
        }

        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(self::$path, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST) as $path) {
            $path->isDir() && !$path->isLink() ? rmdir($path->getPathname()) : unlink($path->getPathname());
        }

        rmdir(self::$path);
    }

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

    public function testResourceInTwig()
    {
        $options = [
            'debug' => true,
            'auto_reload' => true,
            'strict_variables' => false,
            'cache' => false,
        ];

        $loader = new \Twig_Loader_Array(['index.html' => '{% resource \'test:css:style.js\' %}']);

        $twig = new \Twig_Environment($loader, $options);
        $twig->setExtensions([new Resource(true, $this->public, $this->resource)]);

        $twig->render('index.html', ['numberOfApples' => 10]);
    }
}
