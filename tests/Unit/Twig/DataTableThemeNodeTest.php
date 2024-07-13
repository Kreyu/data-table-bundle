<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Twig;

use Kreyu\Bundle\DataTableBundle\Twig\DataTableExtension;
use Kreyu\Bundle\DataTableBundle\Twig\DataTableThemeNode;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Twig\Compiler;
use Twig\Environment;
use Twig\Loader\LoaderInterface;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\NameExpression;
use Twig\Node\Node;

class DataTableThemeNodeTest extends TestCase
{
    public function testConstructor()
    {
        $dataTable = new NameExpression('data_table', 0);

        $themes = new Node([
            new ConstantExpression('foo', 0),
            new ConstantExpression('bar', 0),
        ]);

        $node = new DataTableThemeNode($dataTable, $themes, 0);

        $this->assertEquals($dataTable, $node->getNode('data_table'));
        $this->assertEquals($themes, $node->getNode('themes'));
        $this->assertFalse($node->getAttribute('only'));
    }

    #[DataProvider('provideCompileCases')]
    public function testCompile(DataTableThemeNode $node, array $expected)
    {
        $environment = new Environment($this->createMock(LoaderInterface::class));

        $extension = $this->createMock(DataTableExtension::class);

        $environment->addExtension($extension);

        $compiler = new Compiler($environment);

        // In Twig 2, the ["foo", "bar"] is parsed with as [0 => "foo", 1 => "bar"].
        // In Twig 3, the ["foo", "bar"] is parsed with as ["foo", "bar"].
        // For compatibility, we are checking whether the compiled value is equal to one of the expected values.
        $this->assertThat(trim($compiler->compile($node)->getSource()), $this->logicalOr(
            $this->equalTo($expected[0]),
            $this->equalTo($expected[1]),
        ));
    }

    public static function provideCompileCases(): iterable
    {
        yield 'single theme' => [
            new DataTableThemeNode(
                new NameExpression('data_table', 0),
                new ArrayExpression([
                    new ConstantExpression(0, 0),
                    new ConstantExpression('foo', 0),
                ], 0),
                0,
                'data_table_theme',
                false,
            ), [
                '$this->env->getExtension("Kreyu\\Bundle\\DataTableBundle\\Twig\\DataTableExtension")->setDataTableThemes(($context["data_table"] ?? null), ["foo"], false);',
                '$this->env->getExtension("Kreyu\\Bundle\\DataTableBundle\\Twig\\DataTableExtension")->setDataTableThemes(($context["data_table"] ?? null), [0 => "foo"], false);',
            ],
        ];

        yield 'multiple themes without only' => [
            new DataTableThemeNode(
                new NameExpression('data_table', 0),
                new ArrayExpression([
                    new ConstantExpression(0, 0),
                    new ConstantExpression('foo', 0),
                    new ConstantExpression(1, 0),
                    new ConstantExpression('bar', 0),
                ], 0),
                0,
                'data_table_theme',
                false,
            ), [
                '$this->env->getExtension("Kreyu\\Bundle\\DataTableBundle\\Twig\\DataTableExtension")->setDataTableThemes(($context["data_table"] ?? null), ["foo", "bar"], false);',
                '$this->env->getExtension("Kreyu\\Bundle\\DataTableBundle\\Twig\\DataTableExtension")->setDataTableThemes(($context["data_table"] ?? null), [0 => "foo", 1 => "bar"], false);',
            ],
        ];

        yield 'multiple themes with only' => [
            new DataTableThemeNode(
                new NameExpression('data_table', 0),
                new ArrayExpression([
                    new ConstantExpression(0, 0),
                    new ConstantExpression('foo', 0),
                    new ConstantExpression(1, 0),
                    new ConstantExpression('bar', 0),
                ], 0),
                0,
                'data_table_theme',
                true,
            ), [
                '$this->env->getExtension("Kreyu\\Bundle\\DataTableBundle\\Twig\\DataTableExtension")->setDataTableThemes(($context["data_table"] ?? null), ["foo", "bar"], true);',
                '$this->env->getExtension("Kreyu\\Bundle\\DataTableBundle\\Twig\\DataTableExtension")->setDataTableThemes(($context["data_table"] ?? null), [0 => "foo", 1 => "bar"], true);',
            ],
        ];
    }
}
