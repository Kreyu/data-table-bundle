<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Twig;

use Twig\Node\Expression\ArrayExpression;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;
use Twig\TokenStream;

class DataTableThemeTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): DataTableThemeNode
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        $dataTable = $this->parser->parseExpression();
        $withToken = $stream->nextIf(Token::NAME_TYPE, 'with');
        $themes = $withToken === null ? $this->parseThemeList($stream) : $this->parser->parseExpression();
        $onlyToken = $stream->nextIf(Token::NAME_TYPE, 'only');

        $stream->expect(Token::BLOCK_END_TYPE);

        return new DataTableThemeNode($dataTable, $themes, $lineno, $this->getTag(), $onlyToken !== null);
    }

    public function getTag(): string
    {
        return 'data_table_theme';
    }

    private function parseThemeList(TokenStream $stream): ArrayExpression
    {
        $themes = new ArrayExpression([], $stream->getCurrent()->getLine());

        do {
            $themes->addElement($this->parser->parseExpression());
        } while (!$stream->test(Token::BLOCK_END_TYPE));

        return $themes;
    }
}
