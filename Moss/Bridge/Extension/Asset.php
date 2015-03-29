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

class Asset extends \Twig_Extension
{

    protected $path;

    /**
     * Construct
     *
     * @param string $path
     */
    public function __construct($path = './{asset}')
    {
        $this->path = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            'asset' => new \Twig_SimpleFunction('asset', [$this, 'make'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'asset';
    }

    /**
     * Makes link to passed asset
     *
     * @param $asset
     *
     * @return string
     */
    public function make($asset)
    {
        return strtr($this->path, ['{asset}' => trim($asset, '\\/')]);
    }
}
