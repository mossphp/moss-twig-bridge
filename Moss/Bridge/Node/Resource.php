<?php

/*
 * This file is part of the Moss Twig bridge package
 *
 * (c) Michal Wachowski <wachowski.michal@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Moss\Bridge\Node;

class Resource extends \Twig_Node implements \Twig_NodeOutputInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct(\Twig_Node_Expression $resource, $lineno, $tag = null)
    {
        parent::__construct(array('resource' => $resource), array(), $lineno, $tag);
    }

    /**
     * {@inheritdoc}
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $compiler->addDebugInfo($this);
        $compiler
            ->write('echo $this->env->getExtension(\'resource\')->build(')
            ->subcompile($this->getNode('resource'))
            ->raw(");\n");
    }
}
