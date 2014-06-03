<?php

/*
 * This file is part of the Moss Twig bridge package
 *
 * (c) Michal Wachowski <wachowski.michal@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Moss\Bridge\View;

use Moss\Bag\Bag;
use Moss\View\ViewInterface;

/**
 * Moss view
 * Uses Twig as template engine
 *
 * @package Moss Bridge
 * @author  Michal Wachowski <wachowski.michal@gmail.com>
 */
class View extends Bag implements ViewInterface
{

    protected $template;

    /** @var \Twig_Environment */
    protected $twig;

    /**
     * Creates View instance
     *
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = & $twig;
    }

    /**
     * Assigns template to view
     *
     * @param string $template path to template (supports namespaces)
     *
     * @return View
     */
    public function template($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Renders view
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function render()
    {
        if (!$this->template) {
            throw new \InvalidArgumentException('Undefined view or view file does not exists: ' . $this->template . '!');
        }

        return $this->twig->render($this->template, $this->storage);
    }

    /**
     * Renders and returns view as string
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return (string) $this->render();
        } catch (\InvalidArgumentException $e) {
            return sprintf('%s (%s line:%s)', $e->getMessage(), $e->getFile(), $e->getLine());
        }
    }
}
