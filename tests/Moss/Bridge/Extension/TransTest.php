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


use Moss\Locale\Translator\TranslatorInterface;

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

        $expected = [
            'trans' => new \Twig_SimpleFilter('trans', [$trans, 'trans']),
            'translate' => new \Twig_SimpleFilter('translate', [$trans, 'trans']),
            'transChoice' => new \Twig_SimpleFilter('transChoice', [$trans, 'transChoice']),
        ];

        $this->assertEquals($expected, $result);
    }

    public function testTrans()
    {
        $translator = $this->getMock('\Moss\Locale\Translator\TranslatorInterface');
        $translator->expects($this->once())
            ->method('trans')
            ->with('lorem ipsum', ['foo' => 'bar']);

        $trans = new Trans($translator);
        $trans->trans('lorem ipsum', ['foo' => 'bar']);
    }

    public function testTransChoice()
    {
        $translator = $this->getMock('\Moss\Locale\Translator\TranslatorInterface');
        $translator->expects($this->once())
            ->method('transChoice')
            ->with('lorem ipsum', 10, ['foo' => 'bar']);

        $trans = new Trans($translator);
        $trans->transChoice('lorem ipsum', 10, ['foo' => 'bar']);
    }

    public function testShortInlineTransInTwig()
    {
        $translator = $this->getMock('\Moss\Locale\Translator\TranslatorInterface');
        $translator->expects($this->any())
            ->method('trans')
            ->with('Hello Michal', []);

        $twig = $this->mockTwig($translator, '{% trans \'Hello Michal\' %}');
        $twig->render('index.html');
    }

    public function testInlineTransWithArgumentsInTwig()
    {
        $translator = $this->getMock('\Moss\Locale\Translator\TranslatorInterface');
        $translator->expects($this->any())
            ->method('trans')
            ->with('Hello %name%', ['%name%' => 'Michal']);

        $twig = $this->mockTwig($translator, '{% trans with {\'%name%\': \'Michal\'} \'Hello %name%\' %}');
        $twig->render('index.html');
    }

    public function testInlineTransWithForcedLanguageInTwig()
    {
        $translator = $this->getMock('\Moss\Locale\Translator\TranslatorInterface');
        $translator->expects($this->any())
            ->method('trans')
            ->with('Hello Michal', []);

        $twig = $this->mockTwig($translator, '{% trans \'Hello Michal\' %}');
        $twig->render('index.html');
    }

    public function testInlineTransWithArgumentsAndLanguageInTwig()
    {
        $translator = $this->getMock('\Moss\Locale\Translator\TranslatorInterface');
        $translator->expects($this->any())
            ->method('trans')
            ->with('Hello %name%', ['%name%' => 'Michal']);

        $twig = $this->mockTwig($translator, '{% trans with {\'%name%\': \'Michal\'} \'Hello %name%\' %}');
        $twig->render('index.html');
    }

    public function testBlockTrans()
    {
        $translator = $this->getMock('\Moss\Locale\Translator\TranslatorInterface');
        $translator->expects($this->any())
            ->method('trans')
            ->with('Hello Michal', []);

        $twig = $this->mockTwig($translator, '{% trans %}Hello Michal{% endtrans %}');
        $twig->render('index.html');
    }

    public function testBlockTransWithArgumentsInTwig()
    {
        $translator = $this->getMock('\Moss\Locale\Translator\TranslatorInterface');
        $translator->expects($this->any())
            ->method('trans')
            ->with('Hello %name%', ['%name%' => 'Michal']);

        $twig = $this->mockTwig($translator, '{% trans with {\'%name%\': \'Michal\'} %}Hello %name%{% endtrans %}');
        $twig->render('index.html');
    }

    public function testTransChoiceInTwig()
    {
        $template = <<<'TWIG'
{% transchoice numberOfApples %}
{0} %name%, there are no apples|{1} %name%, there is one apple|]1,Inf] %name%, there are %numberOfApples% apples
{% endtranschoice %}
TWIG;

        $translator = $this->getMock('\Moss\Locale\Translator\TranslatorInterface');
        $translator->expects($this->any())
            ->method('transChoice')
            ->with(
                '{0} %name%, there are no apples|{1} %name%, there is one apple|]1,Inf] %name%, there are %numberOfApples% apples',
                10,
                ['%name%' => null, '%numberOfApples%' => 10]
            );

        $twig = $this->mockTwig($translator, $template);
        $twig->render('index.html', ['numberOfApples' => 10]);
    }

    public function testTransChoiceWithArgumentsInTwig()
    {
        $template = <<<'TWIG'
{% transchoice numberOfApples with {'%name%': 'Michal'} %}
{0} %name%, there are no apples|{1} %name%, there is one apple|]1,Inf] %name%, there are %numberOfApples% apples
{% endtranschoice %}
TWIG;

        $translator = $this->getMock('\Moss\Locale\Translator\TranslatorInterface');
        $translator->expects($this->any())
            ->method('transChoice')
            ->with(
                '{0} %name%, there are no apples|{1} %name%, there is one apple|]1,Inf] %name%, there are %numberOfApples% apples',
                10,
                ['%name%' => 'Michal', '%numberOfApples%' => 10]
            );

        $twig = $this->mockTwig($translator, $template);
        $twig->render('index.html', ['numberOfApples' => 10]);
    }

    public function testTransFromIncludeLikeString()
    {
        $translator = $this->getMock('\Moss\Locale\Translator\TranslatorInterface');
        $translator->expects($this->any())
            ->method('trans')
            ->with('Hello Michal');

        $twig = $this->mockTwig($translator, '{% include template_from_string(tplString) %}');
        $twig->render('index.html', ['tplString' => '{% trans \'Hello Michal\' %}']);
    }

    public function mockTwig(TranslatorInterface $translator, $template)
    {
        $options = [
            'debug' => true,
            'auto_reload' => true,
            'strict_variables' => false,
            'cache' => false,
        ];

        $loader = new \Twig_Loader_Array(
            [
                'index.html' => $template,
            ]
        );

        $twig = new \Twig_Environment($loader, $options);
        $twig->setExtensions(
            [
                new \Twig_Extension_StringLoader(),
                new Trans($translator),
            ]
        );

        return $twig;
    }
}
