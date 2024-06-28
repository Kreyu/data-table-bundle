<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Twig;

use Kreyu\Bundle\DataTableBundle\Column\ColumnSortUrlGeneratorInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Filter\FilterClearUrlGeneratorInterface;
use Kreyu\Bundle\DataTableBundle\Twig\DataTableExtension;
use PHPUnit\Framework\TestCase;

class DataTableExtensionTest extends TestCase
{
    public function testSetDataTableThemes(): void
    {
        $view = new DataTableView();
        $view->vars['themes'] = ['foo'];

        $this->createExtension()->setDataTableThemes($view, ['bar']);

        $this->assertEquals(['foo', 'bar'], $view->vars['themes']);
    }

    public function testSetDataTableThemesWithOnly(): void
    {
        $view = new DataTableView();
        $view->vars['themes'] = ['foo'];

        $this->createExtension()->setDataTableThemes($view, ['bar'], true);

        $this->assertEquals(['bar'], $view->vars['themes']);
    }

    private function createExtension(): DataTableExtension
    {
        return new DataTableExtension(
            $this->createStub(ColumnSortUrlGeneratorInterface::class),
            $this->createStub(FilterClearUrlGeneratorInterface::class),
        );
    }
}
