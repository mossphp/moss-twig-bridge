<?php

/*
 * This file is part of the Moss Twig bridge package
 *
 * (c) Michal Wachowski <wachowski.michal@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Moss\Bridge\Extension;

use Moss\Locale\Formatter\FormatterInterface;

class Format extends \Twig_Extension
{

    protected $formatter;

    public function __construct(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of filters
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('number', [$this, 'formatNumber']),
            new \Twig_SimpleFilter('currency', [$this, 'formatCurrency']),
            new \Twig_SimpleFilter('time', [$this, 'formatTime']),
            new \Twig_SimpleFilter('date', [$this, 'formatDate']),
            new \Twig_SimpleFilter('dateTime', [$this, 'formatDateTime']),
        );
    }

    public function formatNumber($number)
    {
        return $this->formatter->formatNumber($number);
    }

    public function formatCurrency($amount)
    {
        return $this->formatter->formatCurrency($amount);
    }

    public function formatTime(\DateTime $date)
    {
        return $this->formatter->formatTime($date);
    }

    public function formatDate(\DateTime $date)
    {
        return $this->formatDate($date);
    }

    public function formatDateTime(\DateTime $date)
    {
        return $this->formatter->formatDateTime($date);
    }

    public function getName()
    {
        return 'formatter';
    }
}
