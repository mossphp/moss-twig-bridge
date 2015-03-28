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

use Moss\Bridge\TokenParser\Trans as TokenParserTrans;
use Moss\Bridge\TokenParser\TransChoice as TokenParserTransChoice;
use Moss\Locale\Translator\TranslatorInterface;

class Trans extends \Twig_Extension
{
    private $translator;

    public function __construct(TranslatorInterface $translator = null)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            'trans' => new \Twig_SimpleFilter('trans', [$this, 'trans']),
            'translate' => new \Twig_SimpleFilter('translate', [$this, 'trans']),
            'transChoice' => new \Twig_SimpleFilter('transChoice', [$this, 'transChoice']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return array(
            // {% trans "Symfony ain't that great!" %}
            // {% trans %}Symfony ain't that great!{% endtrans %}
            new TokenParserTrans(),

            // {% transchoice count %}
            //      '{0} There are no apples|{1} There is one apple|]1,19] There are %count% apples|[20,Inf] There are many apples'
            // {% endtranschoice %}
            new TokenParserTransChoice(),
        );
    }

    /**
     * Translates singular text
     *
     * @param string $message
     * @param array  $arguments
     *
     * @return string
     */
    public function trans($message, array $arguments = array())
    {
        return !$this->translator ? strtr($message, $arguments) : $this->translator->trans($message, $arguments);
    }

    /**
     * Translates plural text
     *
     * @param string $message
     * @param string $count
     * @param array  $arguments
     *
     * @return string
     */
    public function transChoice($message, $count, array $arguments = array())
    {
        return !$this->translator ? strtr($message, $arguments) : $this->translator->transChoice($message, $count, $arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'translator';
    }
}
