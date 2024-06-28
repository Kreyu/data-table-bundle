<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Twig;

use Kreyu\Bundle\DataTableBundle\Twig\DataTableThemeNode;
use Kreyu\Bundle\DataTableBundle\Twig\DataTableThemeTokenParser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\LoaderInterface;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\NameExpression;
use Twig\Parser;
use Twig\Source;

class DataTableThemeTokenParserTest extends TestCase
{
    #[DataProvider('provideCompileCases')]
    public function testCompile($source, $expected)
    {
        $env = new Environment($this->createMock(LoaderInterface::class), ['cache' => false, 'autoescape' => false, 'optimizations' => 0]);
        $env->addTokenParser(new DataTableThemeTokenParser());

        $source = new Source($source, '');
        $stream = $env->tokenize($source);
        $parser = new Parser($env);

        $expected->setSourceContext($source);

        $this->assertEquals($expected, $parser->parse($stream)->getNode('body')->getNode('0'));
    }

    public static function provideCompileCases(): iterable
    {
        yield 'single theme' => [
            '{% data_table_theme data_table "foo" %}',
            new DataTableThemeNode(
                new NameExpression('data_table', 1),
                new ArrayExpression([
                    new ConstantExpression(0, 1),
                    new ConstantExpression('foo', 1),
                ], 1),
                1,
                'data_table_theme',
                false,
            ),
        ];

        yield 'multiple themes without only' => [
            '{% data_table_theme data_table with ["foo", "bar"] %}',
            new DataTableThemeNode(
                new NameExpression('data_table', 1),
                new ArrayExpression([
                    new ConstantExpression(0, 1),
                    new ConstantExpression('foo', 1),
                    new ConstantExpression(1, 1),
                    new ConstantExpression('bar', 1),
                ], 1),
                1,
                'data_table_theme',
                false,
            ),
        ];

        yield 'multiple themes with only' => [
            '{% data_table_theme data_table with ["foo", "bar"] only %}',
            new DataTableThemeNode(
                new NameExpression('data_table', 1),
                new ArrayExpression([
                    new ConstantExpression(0, 1),
                    new ConstantExpression('foo', 1),
                    new ConstantExpression(1, 1),
                    new ConstantExpression('bar', 1),
                ], 1),
                1,
                'data_table_theme',
                true,
            ),
        ];
    }
}