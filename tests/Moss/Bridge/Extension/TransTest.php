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
            ->method('translate')
            ->with('lorem ipsum', array('%foo%' => 'bar'), 'en');

        $trans = new Trans($translator);
        $trans->trans('lorem ipsum', array('foo' => 'bar'), 'en');
    }

    public function testTransChoice()
    {
        $translator = $this->getMock('\Moss\Locale\Translator\TranslatorInterface');
        $translator->expects($this->once())
            ->method('translatePlural')
            ->with('lorem ipsum', 10, array('%foo%' => 'bar'), 'en');

        $trans = new Trans($translator);
        $trans->transchoice('lorem ipsum', 10, array('foo' => 'bar'), 'en');
    }

    public function testShortInlineTransInTwig()
    {
        $translator = $this->getMock('\Moss\Locale\Translator\TranslatorInterface');
        $translator->expects($this->any())
            ->method('trans')
            ->with('Hello Michal', [], null);

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
            ->with('Hello Michal', [], 'pl');

        $twig = $this->mockTwig($translator, '{% trans into \'pl\' \'Hello Michal\' %}');
        $twig->render('index.html');
    }

    public function testInlineTransWithArgumentsAndLanguageInTwig()
    {
        $translator = $this->getMock('\Moss\Locale\Translator\TranslatorInterface');
        $translator->expects($this->any())
            ->method('trans')
            ->with('Hello %name%', ['%name%' => 'Michal'], 'pl');

        $twig = $this->mockTwig($translator, '{% trans with {\'%name%\': \'Michal\'} into \'pl\' \'Hello %name%\' %}');
        $twig->render('index.html');
    }

    public function testBlockTrans()
    {
        $translator = $this->getMock('\Moss\Locale\Translator\TranslatorInterface');
        $translator->expects($this->any())
            ->method('trans')
            ->with('Hello Michal', [], null);

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

    public function testBlockTransWithForcedLanguageInTwig()
    {
        $translator = $this->getMock('\Moss\Locale\Translator\TranslatorInterface');
        $translator->expects($this->any())
            ->method('trans')
            ->with('Hello Michal', [], 'pl');

        $twig = $this->mockTwig($translator, '{% trans into \'pl\' %}Hello Michal{% endtrans %}');
        $twig->render('index.html');
    }

    public function testBlockTransWithArgumentsAndLanguageInTwig()
    {
        $translator = $this->getMock('\Moss\Locale\Translator\TranslatorInterface');
        $translator->expects($this->any())
            ->method('trans')
            ->with('Hello %name%', ['%name%' => 'Michal'], 'pl');

        $twig = $this->mockTwig($translator, '{% trans with {\'%name%\': \'Michal\'} into \'pl\' %}Hello %name%{% endtrans %}');
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

    public function testTransChoiceWithForcedLanguageInTwig()
    {
        $template = <<<'TWIG'
{% transchoice numberOfApples into "pl" %}
{0} %name%, there are no apples|{1} %name%, there is one apple|]1,Inf] %name%, there are %numberOfApples% apples
{% endtranschoice %}
TWIG;

        $translator = $this->getMock('\Moss\Locale\Translator\TranslatorInterface');
        $translator->expects($this->any())
            ->method('transChoice')
            ->with(
                '{0} %name%, there are no apples|{1} %name%, there is one apple|]1,Inf] %name%, there are %numberOfApples% apples',
                10,
                ['%name%' => null, '%numberOfApples%' => 10],
                'pl'
            );

        $twig = $this->mockTwig($translator, $template);
        $twig->render('index.html', ['numberOfApples' => 10]);
    }

    public function testTransChoiceWithArgumentsAndLanguageInTwig()
    {
        $template = <<<'TWIG'
{% transchoice numberOfApples with {'%name%': 'Michal'} into "pl" %}
{0} %name%, there are no apples|{1} %name%, there is one apple|]1,Inf] %name%, there are %numberOfApples% apples
{% endtranschoice %}
TWIG;

        $translator = $this->getMock('\Moss\Locale\Translator\TranslatorInterface');
        $translator->expects($this->any())
            ->method('transChoice')
            ->with(
                '{0} %name%, there are no apples|{1} %name%, there is one apple|]1,Inf] %name%, there are %numberOfApples% apples',
                10,
                ['%name%' => 'Michal', '%numberOfApples%' => 10],
                'pl'
            );

        $twig = $this->mockTwig($translator, $template);
        $twig->render('index.html', ['numberOfApples' => 10]);
    }

    public function mockTwig(TranslatorInterface $translator, $template) {
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
                new Trans($translator),
            ]
        );

        return $twig;
    }
}
