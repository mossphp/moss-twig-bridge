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


class TransTest extends \PHPUnit_Framework_TestCase
{
    public function testName()
    {
        $translator = $this->getMock('\Moss\Locale\Translator\TranslatorInterface');

        $trans = new Trans($translator);

        $this->assertEquals('translator', $trans->getName());
    }

    public function testFilters()
    {
        $translator = $this->getMock('\Moss\Locale\Translator\TranslatorInterface');

        $trans = new Trans($translator);
        $result = $trans->getFilters();

        $expected = array(
            'trans' => new \Twig_Filter_Method($trans, 'trans'),
            'translate' => new \Twig_Filter_Method($trans, 'trans'),
            'transchoice' => new \Twig_Filter_Method($trans, 'transchoice'),
        );

        $this->assertEquals($expected, $result);
    }

    public function testFunctions()
    {
        $translator = $this->getMock('\Moss\Locale\Translator\TranslatorInterface');

        $trans = new Trans($translator);
        $result = $trans->getFunctions();

        $expected = array(
            'string' => new \Twig_SimpleFunction('string', array($trans, 'fromString'), array('needs_environment' => true)),
        );

        $this->assertEquals($expected, $result);
    }

    public function testTrans()
    {
        $translator = $this->getMock('\Moss\Locale\Translator\TranslatorInterface');
        $translator->expects($this->once())
            ->method('trans')
            ->with('lorem ipsum', array('foo' => 'bar'), 'en');

        $trans = new Trans($translator);
        $trans->trans('lorem ipsum', array('foo' => 'bar'), 'en');
    }

    public function testTransChoice()
    {
        $translator = $this->getMock('\Moss\Locale\Translator\TranslatorInterface');
        $translator->expects($this->once())
            ->method('transChoice')
            ->with('lorem ipsum', 10, array('foo' => 'bar'), 'en');

        $trans = new Trans($translator);
        $trans->transchoice('lorem ipsum', 10, array('foo' => 'bar'), 'en');
    }
}
