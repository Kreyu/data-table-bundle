<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Twig;

use Kreyu\Bundle\DataTableBundle\Column\ColumnSortUrlGeneratorInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Filter\FilterClearUrlGeneratorInterface;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationUrlGeneratorInterface;
use Kreyu\Bundle\DataTableBundle\Twig\DataTableExtension;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

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

    public function testRenderThemeBlockRendersLastOccurrence(): void
    {
        $environment = new Environment(new ArrayLoader([
            'base.html.twig' => '',
            // This theme contains the block, and is the first occurrence.
            'bootstrap_5.html.twig' => <<<TWIG
                {% block content -%}
                    Hello from bootstrap_5.html.twig
                {%- endblock %}
            TWIG,
            // This theme contains the block, but is second occurrence, so it overrides the Bootstrap 5.
            'tabler.html.twig' => <<<TWIG
                {% block content -%}
                    Hello from tabler.html.twig
                {%- endblock %}
            TWIG,
        ]));

        $dataTable = new DataTableView();
        $dataTable->vars['themes'] = ['base.html.twig', 'bootstrap_5.html.twig', 'tabler.html.twig'];

        $html = $this->createExtension()->renderThemeBlock(
            environment: $environment,
            context: [],
            dataTable: $dataTable,
            blockName: 'content',
        );

        $this->assertEquals('Hello from tabler.html.twig', $html);
    }

    public function testRenderThemeBlockThrowsExceptionOnMissingBlock(): void
    {
        $environment = new Environment(new ArrayLoader([
            'base.html.twig' => '',
            'bootstrap_5.html.twig' => '',
            'tabler.html.twig' => '',
        ]));

        $dataTable = new DataTableView();
        $dataTable->vars['themes'] = ['base.html.twig', 'bootstrap_5.html.twig', 'tabler.html.twig'];

        $this->expectExceptionMessage('Block "content" does not exist on any of the configured data table themes: "base.html.twig", "bootstrap_5.html.twig", "tabler.html.twig"');

        $this->createExtension()->renderThemeBlock(
            environment: $environment,
            context: [],
            dataTable: $dataTable,
            blockName: 'content',
        );
    }

    public function testRenderThemeBlockPassesContext(): void
    {
        $environment = new Environment(new ArrayLoader([
            'base.html.twig' => <<<TWIG
                {% block content -%}
                    {{ label }}
                {%- endblock %}
            TWIG,
        ]));

        $dataTable = new DataTableView();
        $dataTable->vars['themes'] = ['base.html.twig'];

        $html = $this->createExtension()->renderThemeBlock(
            environment: $environment,
            context: ['label' => 'Hello World'],
            dataTable: $dataTable,
            blockName: 'content',
        );

        $this->assertEquals('Hello World', $html);
    }

    public function testRenderThemeBlockWithResetAttr(): void
    {
        $environment = new Environment(new ArrayLoader([
            'base.html.twig' => <<<TWIG
                {% block content -%}
                    {{ attr.label ?? 'n/a' }}
                {%- endblock %}
            TWIG,
        ]));

        $dataTable = new DataTableView();
        $dataTable->vars['themes'] = ['base.html.twig'];

        $html = $this->createExtension()->renderThemeBlock(
            environment: $environment,
            context: ['label' => 'Hello World'],
            dataTable: $dataTable,
            blockName: 'content',
            resetAttr: true,
        );

        $this->assertEquals('n/a', $html);
    }

    private function createExtension(): DataTableExtension
    {
        return new DataTableExtension(
            $this->createStub(ColumnSortUrlGeneratorInterface::class),
            $this->createStub(FilterClearUrlGeneratorInterface::class),
            $this->createStub(PaginationUrlGeneratorInterface::class),
        );
    }
}
