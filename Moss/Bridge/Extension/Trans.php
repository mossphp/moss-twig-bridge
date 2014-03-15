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

use Moss\Bridge\Loader\String;
use Moss\Bridge\TokenParser\Trans as TokenParserTrans;
use Moss\Bridge\TokenParser\TransChoice  as TokenParserTransChoice;
use Moss\Locale\LocaleInterface;

class Trans extends \Twig_Extension
{
    private $translator;
    private $stringLoader;

    public function __construct(LocaleInterface $translator = null)
    {
        $this->translator = $translator;
        $this->stringLoader = new String();
    }

    public function getFilters()
    {
        return array(
            'trans' => new \Twig_Filter_Method($this, 'trans'),
            'translate' => new \Twig_Filter_Method($this, 'trans'),
            'transchoice' => new \Twig_Filter_Method($this, 'transchoice'),
        );
    }

    public function getFunctions()
    {
        return array(
            'string' => new \Twig_SimpleFunction('string', array($this, 'fromString'), array('needs_environment' => true)),
        );
    }

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

    public function trans($message, array $arguments = array(), $locale = null)
    {
        if (!$this->translator) {
            return strtr($message, $arguments);
        }

        return $this->translator->trans($message, $arguments, $locale);
    }

    public function transchoice($message, $count, array $arguments = array(), $locale = null)
    {
        if (!$this->translator) {
            return strtr($message, $arguments);
        }

        return $this->translator->transChoice($message, $count, array_merge(array('%count%' => $count), $arguments), $locale);
    }

    public function getName()
    {
        return 'translator';
    }

    public function fromString(\Twig_Environment $env, $template)
    {
        $current = array(
            'cache' => $env->getCache(),
            'loader' => $env->getLoader()
        );

        $env->setCache(false);
        $env->setLoader($this->stringLoader);

        try {
            $template = $env->loadTemplate($this->escape($template));

            $env->setCache($current['cache']);
            $env->setLoader($current['loader']);

            return $template;
        } catch (\Exception $e) {
            $env->setCache($current['cache']);
            $env->setLoader($current['loader']);

            throw $e;
        }
    }

    protected function escape($string)
    {
        return preg_replace_callback(
            '/({% +trans +[\'"])(.*)([\'"] +%})/i',
            function ($match) {
                return $match[1] . preg_replace('/\\\\*[\'"]/im', '&quote;', $match[2]) . $match[3];
            },
            $string
        );
    }
}
