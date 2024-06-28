<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Twig;

use Twig\Node\Expression\ArrayExpression;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

class DataTableThemeTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): DataTableThemeNode
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        $dataTable = $this->parser->getExpressionParser()->parseExpression();
        $only = false;

        if ($this->parser->getStream()->test(Token::NAME_TYPE, 'with')) {
            $this->parser->getStream()->next();

            $themes = $this->parser->getExpressionParser()->parseExpression();

            if ($this->parser->getStream()->nextIf(Token::NAME_TYPE, 'only')) {
                $only = true;
            }
        } else {
            $themes = new ArrayExpression([], $stream->getCurrent()->getLine());

            do {
                $themes->addElement($this->parser->getExpressionParser()->parseExpression());
            } while (!$stream->test(Token::BLOCK_END_TYPE));
        }

        $stream->expect(Token::BLOCK_END_TYPE);

        return new DataTableThemeNode($dataTable, $themes, $lineno, $this->getTag(), $only);
    }

    public function getTag(): string
    {
        return 'data_table_theme';
    }
}