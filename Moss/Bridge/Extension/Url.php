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

use Moss\Http\Router\RouterInterface;

class Url extends \Twig_Extension
{

    protected $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            'url' => new \Twig_SimpleFunction('url', [$this, 'url'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Creates url
     *
     * @param string  $identifier
     * @param array $arguments
     *
     * @return string
     */
    public function url($identifier = null, $arguments = [])
    {
        return $this->router->make($identifier, $arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'url';
    }
}
