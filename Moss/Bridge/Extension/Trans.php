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
use Moss\Bridge\TokenParser\TransChoice as TokenParserTransChoice;
use Moss\Locale\Translator\TranslatorInterface;

class Trans extends \Twig_Extension
{
    private $translator;
    private $stringLoader;

    public function __construct(TranslatorInterface $translator = null)
    {
        $this->translator = $translator;
        $this->stringLoader = new String();
    }

    public function getFilters()
    {
        return [
            'trans' => new \Twig_SimpleFilter('trans', [$this, 'trans']),
            'translate' => new \Twig_SimpleFilter('translate', [$this, 'trans']),
            'transchoice' => new \Twig_SimpleFilter('transchoice', [$this, 'transchoice']),
        ];
    }

    public function getFunctions()
    {
        return array(
            'string' => new \Twig_SimpleFunction('string', [$this, 'fromString'], ['needs_environment' => true]),
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

    public function trans($message, array $arguments = array())
    {
       return !$this->translator ? strtr($message, $arguments) : $this->translator->trans($message, $arguments);
    }

    public function transChoice($message, $count, array $arguments = array())
    {
        return !$this->translator ? strtr($message, $arguments) : $this->translator->transChoice($message, $count, $arguments);
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
