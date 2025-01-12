<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\HtmlColumnType;
use Kreyu\Bundle\DataTableBundle\Test\Column\Type\ColumnTypeTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class HtmlColumnTypeTest extends ColumnTypeTestCase
{
    protected function getTestedColumnType(): ColumnTypeInterface
    {
        return new HtmlColumnType();
    }

    public function testDefaultRawOption(): void
    {
        $column = $this->createColumn();
        $columnValueView = $this->createColumnValueView($column);

        $this->assertTrue($columnValueView->vars['raw']);
    }

    #[DataProvider('provideRawOption')]
    public function testPassingRawOption(bool $raw): void
    {
        $column = $this->createColumn(['raw' => $raw]);
        $columnValueView = $this->createColumnValueView($column);

        $this->assertEquals($raw, $columnValueView->vars['raw']);
    }

    public static function provideRawOption(): iterable
    {
        yield 'true' => [true];
        yield 'false' => [false];
    }

    public function testDefaultStripTagsOption(): void
    {
        $column = $this->createColumn();
        $columnValueView = $this->createColumnValueView($column);

        $this->assertFalse($columnValueView->vars['strip_tags']);
    }

    #[DataProvider('provideStripTagsOption')]
    public function testPassingStripTagsOption(bool $stripTags): void
    {
        $column = $this->createColumn(['strip_tags' => $stripTags]);
        $columnValueView = $this->createColumnValueView($column);

        $this->assertEquals($stripTags, $columnValueView->vars['strip_tags']);
    }

    public static function provideStripTagsOption(): iterable
    {
        yield 'true' => [true];
        yield 'false' => [false];
    }

    public function testDefaultAllowedTagsOption(): void
    {
        $column = $this->createColumn();
        $columnValueView = $this->createColumnValueView($column);

        $this->assertNull($columnValueView->vars['allowed_tags']);
    }

    #[DataProvider('provideAllowedTagsOption')]
    public function testPassingAllowedTagsOption(null|string|array $allowedTags): void
    {
        $column = $this->createColumn(['allowed_tags' => $allowedTags]);
        $columnValueView = $this->createColumnValueView($column);

        $this->assertEquals($allowedTags, $columnValueView->vars['allowed_tags']);
    }

    public static function provideAllowedTagsOption(): iterable
    {
        yield 'null' => [null];
        yield 'string' => ['<strong><br>'];
        yield 'array of strings' => [['<strong>', '<br>']];
    }
}
