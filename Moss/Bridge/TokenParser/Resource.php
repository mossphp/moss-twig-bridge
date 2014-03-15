<?php

/*
 * This file is part of the Moss Twig bridge package
 *
 * (c) Michal Wachowski <wachowski.michal@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Moss\Bridge\TokenParser;

use Moss\Bridge\Node\Resource as NodeResource;

class Resource extends \Twig_TokenParser
{

    public function parse(\Twig_Token $token)
    {
        $resource = $this->parser
            ->getExpressionParser()
            ->parseExpression();

        $this->parser
            ->getStream()
            ->expect(\Twig_Token::BLOCK_END_TYPE);

        return new NodeResource($resource, $token->getLine(), $this->getTag());
    }

    public function getTag()
    {
        return 'resource';
    }
}
