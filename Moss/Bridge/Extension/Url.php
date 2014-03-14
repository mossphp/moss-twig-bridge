<?php

/*
 * This file is part of the Storage package
 *
 * (c) Michal Wachowski <wachowski.michal@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Moss\Bridge\Extension;

class Url extends \Twig_Extension
{

    protected $router;

    public function __construct(\Moss\http\router\Router $router)
    {
        $this->router = & $router;
    }

    public function getFunctions()
    {
        return array(
            'url' => new \Twig_Function_Method($this, 'url'),
        );
    }

    public function url($identifier = null, $arguments = array())
    {
        return $this->router->make($identifier, $arguments);
    }

    public function getName()
    {
        return 'url';
    }
}