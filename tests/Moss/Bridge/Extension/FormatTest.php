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


use Moss\Locale\Formatter\FormatterInterface;

class FormatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormatterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $formatter;

    protected function setUp()
    {
        parent::setUp();
        $this->formatter = $this->getMock('\Moss\Locale\Formatter\FormatterInterface');
    }


    protected function buildTwig($template)
    {
        $twig = new \Twig_Environment(
            new \Twig_Loader_Array(['index.html' => $template]),
            [
                'debug' => true,
                'auto_reload' => true,
                'strict_variables' => false,
                'cache' => false,
            ]
        );

        $twig->setExtensions([new Format($this->formatter)]);

        return $twig;
    }


    public function testFormatNumberFunction()
    {
        $this->formatter->expects($this->once())->method('formatNumber')->will($this->returnValue('123,456.789'));

        $format = new Format($this->formatter);
        $format->formatNumber(123456.789);
    }

    public function testFormatCurrencyFunction()
    {
        $this->formatter->expects($this->once())->method('formatCurrency')->will($this->returnValue('$1,234,567.89'));

        $format = new Format($this->formatter);
        $format->formatCurrency(123456789);
    }

    public function testFormatTimeFunction()
    {
        $this->formatter->expects($this->once())->method('formatTime')->will($this->returnValue('1:01 PM'));

        $format = new Format($this->formatter);
        $format->formatTime(new \DateTime('2015-03-05 13:01', new \DateTimeZone('UTC')));
    }

    public function testFormatDateFunction()
    {
        $this->formatter->expects($this->once())->method('formatDate')->will($this->returnValue('3/5/15'));

        $format = new Format($this->formatter);
        $format->formatDate(new \DateTime('2015-03-05 13:01', new \DateTimeZone('UTC')));
    }

    public function testFormatDateTimeFunction()
    {
        $this->formatter->expects($this->once())->method('formatDateTime')->will($this->returnValue('3/5/15, 1:01 PM'));

        $format = new Format($this->formatter);
        $format->formatDateTime(new \DateTime('2015-03-05 13:01', new \DateTimeZone('UTC')));
    }

    public function testFormatNumberInTwig()
    {
        $this->formatter->expects($this->once())->method('formatNumber')->will($this->returnValue('123,456.789'));

        $twig = $this->buildTwig('{{ value|number }}');
        $result = $twig->render('index.html', ['value' => 123456.789]);

        $this->assertEquals('123,456.789', $result);
    }

    public function testFormatCurrencyInTwig()
    {
        $this->formatter->expects($this->once())->method('formatCurrency')->will($this->returnValue('$1,234,567.89'));

        $twig = $this->buildTwig('{{ value|currency }}');
        $result = $twig->render('index.html', ['value' => 123456789]);

        $this->assertEquals('$1,234,567.89', $result);
    }

    public function testFormatTimeInTwig()
    {
        $this->formatter->expects($this->once())->method('formatTime')->will($this->returnValue('1:01 PM'));

        $twig = $this->buildTwig('{{ value|time }}');
        $result = $twig->render('index.html', ['value' => new \DateTime('2015-03-05 13:01', new \DateTimeZone('UTC'))]);

        $this->assertEquals('1:01 PM', $result);
    }

    public function testFormatDateInTwig()
    {
        $this->formatter->expects($this->once())->method('formatDate')->will($this->returnValue('3/5/15'));

        $twig = $this->buildTwig('{{ value|date }}');
        $result = $twig->render('index.html', ['value' => new \DateTime('2015-03-05 13:01', new \DateTimeZone('UTC'))]);

        $this->assertEquals('3/5/15', $result);
    }

    public function testFormatDateTimeInTwig()
    {
        $this->formatter->expects($this->once())->method('formatDateTime')->will($this->returnValue('3/5/15, 1:01 PM'));

        $twig = $this->buildTwig('{{ value|dateTime }}');
        $result = $twig->render('index.html', ['value' => new \DateTime('2015-03-05 13:01', new \DateTimeZone('UTC'))]);

        $this->assertEquals('3/5/15, 1:01 PM', $result);
    }
}
