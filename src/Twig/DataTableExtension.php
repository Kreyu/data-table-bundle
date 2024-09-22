<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Twig;

use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnHeaderView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnSortUrlGeneratorInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exception\LogicException;
use Kreyu\Bundle\DataTableBundle\Filter\FilterClearUrlGeneratorInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterView;
use Kreyu\Bundle\DataTableBundle\HeaderRowView;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationUrlGeneratorInterface;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationView;
use Kreyu\Bundle\DataTableBundle\ValueRowView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Twig\Environment;
use Twig\Error\Error as TwigException;
use Twig\Error\RuntimeError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DataTableExtension extends AbstractExtension
{
    public function __construct(
        private ColumnSortUrlGeneratorInterface $columnSortUrlGenerator,
        private FilterClearUrlGeneratorInterface $filterClearUrlGenerator,
        private PaginationUrlGeneratorInterface $paginationUrlGenerator,
    ) {
    }

    public function getFunctions(): array
    {
        $renderOptions = [
            'needs_environment' => true,
            'is_safe' => ['html'],
        ];

        return [
            new TwigFunction('data_table', $this->renderDataTable(...), $renderOptions),
            new TwigFunction('data_table_form_aware', $this->renderDataTableFormAware(...), $renderOptions),
            new TwigFunction('data_table_table', $this->renderTable(...), $renderOptions),
            new TwigFunction('data_table_action_bar', $this->renderActionBar(...), $renderOptions),
            new TwigFunction('data_table_header_row', $this->renderHeaderRow(...), $renderOptions),
            new TwigFunction('data_table_value_row', $this->renderValueRow(...), $renderOptions),
            new TwigFunction('data_table_column_label', $this->renderColumnLabel(...), $renderOptions),
            new TwigFunction('data_table_column_header', $this->renderColumnHeader(...), $renderOptions),
            new TwigFunction('data_table_column_value', $this->renderColumnValue(...), $renderOptions),
            new TwigFunction('data_table_action', $this->renderAction(...), $renderOptions),
            new TwigFunction('data_table_pagination', $this->renderPagination(...), $renderOptions),
            new TwigFunction('data_table_filters_form', $this->renderFiltersForm(...), $renderOptions),
            new TwigFunction('data_table_personalization_form', $this->renderPersonalizationForm(...), $renderOptions),
            new TwigFunction('data_table_export_form', $this->renderExportForm(...), $renderOptions),
            new TwigFunction('data_table_filter_clear_url', $this->generateFilterClearUrl(...)),
            new TwigFunction('data_table_column_sort_url', $this->generateColumnSortUrl(...)),
            new TwigFunction('data_table_pagination_url', $this->generatePaginationUrl(...)),
        ];
    }

    public function getTokenParsers(): array
    {
        return [
            new DataTableThemeTokenParser(),
        ];
    }

    public function setDataTableThemes(DataTableView $view, array $themes, bool $only = false): void
    {
        if ($only) {
            $view->vars['themes'] = $themes;
        } else {
            array_push($view->vars['themes'], ...$themes);
        }
    }

    /**
     * @throws TwigException
     * @throws LogicException if none of the data table themes support given block
     */
    public function renderDataTable(Environment $environment, DataTableView $view, array $vars = []): string
    {
        return $this->renderBlock($environment, $view, 'data_table', array_merge($view->vars, $vars));
    }

    /**
     * @throws TwigException
     * @throws LogicException if none of the data table themes support given block
     */
    public function renderTable(Environment $environment, DataTableView $view, array $vars = []): string
    {
        return $this->renderBlock($environment, $view, 'table', array_merge($view->vars, $vars));
    }

    /**
     * @throws TwigException
     * @throws LogicException if none of the data table themes support given block
     */
    public function renderActionBar(Environment $environment, DataTableView $view, array $vars = []): string
    {
        return $this->renderBlock($environment, $view, 'action_bar', array_merge($view->vars, $vars));
    }

    /**
     * @throws TwigException
     * @throws LogicException if none of the data table themes support given block
     */
    public function renderHeaderRow(Environment $environment, HeaderRowView $view, array $vars = []): string
    {
        return $this->renderBlock($environment, $view->parent, 'header_row', array_merge($view->vars, $vars));
    }

    /**
     * @throws TwigException
     * @throws LogicException if none of the data table themes support given block
     */
    public function renderValueRow(Environment $environment, ValueRowView $view, array $vars = []): string
    {
        return $this->renderBlock($environment, $view->parent, 'value_row', array_merge($view->vars, $vars));
    }

    /**
     * @throws TwigException
     * @throws LogicException if none of the data table themes support any block from the hierarchy
     */
    public function renderColumnLabel(Environment $environment, ColumnHeaderView $view, array $vars = []): string
    {
        return $this->renderHierarchyAwareViewBlock($environment, $view, 'label', array_merge($view->vars, $vars));
    }

    /**
     * @throws TwigException
     * @throws LogicException if none of the data table themes support any block from the hierarchy
     */
    public function renderColumnHeader(Environment $environment, ColumnHeaderView $view, array $vars = []): string
    {
        return $this->renderHierarchyAwareViewBlock($environment, $view, 'header', array_merge($view->vars, $vars));
    }

    /**
     * @throws TwigException
     * @throws LogicException if none of the data table themes support any block from the hierarchy
     */
    public function renderColumnValue(Environment $environment, ColumnValueView $view, array $vars = []): string
    {
        return $this->renderHierarchyAwareViewBlock($environment, $view, 'value', array_merge($view->vars, $vars));
    }

    /**
     * @throws TwigException
     * @throws LogicException if none of the data table themes support any block from the hierarchy
     */
    public function renderAction(Environment $environment, ActionView $view, array $vars = []): string
    {
        return $this->renderHierarchyAwareViewBlock($environment, $view, 'control', array_merge($view->vars, $vars));
    }

    /**
     * @throws TwigException
     * @throws LogicException if none of the data table themes support given block
     */
    public function renderPagination(Environment $environment, DataTableView|PaginationView $view, array $vars = []): string
    {
        if ($view instanceof DataTableView) {
            $view = $view->vars['pagination'];
        }

        return $this->renderBlock($environment, $view->parent, 'pagination', array_merge($view->vars, $vars));
    }

    /**
     * @throws TwigException
     * @throws LogicException if none of the data table themes support given block
     */
    public function renderFiltersForm(Environment $environment, FormInterface|FormView $form, array $vars = []): string
    {
        if ($form instanceof FormInterface) {
            $form = $form->createView();
        }

        return $this->renderBlock($environment, $form->vars['data_table_view'], 'kreyu_data_table_filters_form', array_merge($vars, ['form' => $form]));
    }

    /**
     * @throws TwigException
     * @throws LogicException if none of the data table themes support given block
     */
    public function renderPersonalizationForm(Environment $environment, FormInterface|FormView $form, array $vars = []): string
    {
        if ($form instanceof FormInterface) {
            $form = $form->createView();
        }

        return $this->renderBlock($environment, $form->vars['data_table_view'], 'kreyu_data_table_personalization_form', array_merge($vars, ['form' => $form]));
    }

    /**
     * @throws TwigException
     * @throws LogicException if none of the data table themes support given block
     */
    public function renderExportForm(Environment $environment, FormInterface|FormView $form, array $vars = []): string
    {
        if ($form instanceof FormInterface) {
            $form = $form->createView();
        }

        return $this->renderBlock($environment, $form->vars['data_table_view'], 'kreyu_data_table_export_form', array_merge($vars, ['form' => $form]));
    }

    public function generateFilterClearUrl(DataTableView $dataTableView, FilterView|array $filterViews): string
    {
        if ($filterViews instanceof FilterView) {
            $filterViews = [$filterViews];
        }

        return $this->filterClearUrlGenerator->generate($dataTableView, ...$filterViews);
    }

    public function generateColumnSortUrl(DataTableView $dataTableView, ColumnHeaderView|array $columnHeaderViews): string
    {
        if ($columnHeaderViews instanceof ColumnHeaderView) {
            $columnHeaderViews = [$columnHeaderViews];
        }

        return $this->columnSortUrlGenerator->generate($dataTableView, ...$columnHeaderViews);
    }

    public function generatePaginationUrl(DataTableView $dataTableView, int $page): string
    {
        return $this->paginationUrlGenerator->generate($dataTableView, $page);
    }

    /**
     * @throws TwigException
     * @throws LogicException if none of the data table themes support given block
     */
    private function renderBlock(Environment $environment, DataTableView $dataTable, string $blockName, array $context = []): string
    {
        foreach ($dataTable->vars['themes'] as $theme) {
            $wrapper = $environment->load($theme);

            if ($wrapper->hasBlock($blockName, $context)) {
                $context['theme'] = $theme;

                return $wrapper->renderBlock($blockName, $context);
            }
        }

        throw new LogicException(sprintf(
            'Unable to render block "%s" as it is not supported by any of the following themes: "%s".',
            $blockName,
            implode('", "', $dataTable->vars['themes'])
        ));
    }

    /**
     * @throws TwigException
     * @throws LogicException if none of the data table themes support any block from the hierarchy
     */
    private function renderHierarchyAwareViewBlock(Environment $environment, ColumnHeaderView|ColumnValueView|ActionView $view, string $blockNameSuffix, array $context = []): string
    {
        $dataTable = $view->getDataTable();

        $blockNamePrefix = match ($view::class) {
            ColumnHeaderView::class, ColumnValueView::class => 'column',
            ActionView::class => 'action',
        };

        $blockNameHierarchy = [];

        foreach ($view->vars['block_prefixes'] as $blockPrefix) {
            $blockName = $blockPrefix.'_'.$blockNameSuffix;

            if ($blockNamePrefix !== $blockPrefix) {
                $blockName = $blockNamePrefix.'_'.$blockName;
            }

            foreach ($dataTable->vars['themes'] as $theme) {
                $wrapper = $environment->load($theme);

                if ($wrapper->hasBlock($blockName, $context)) {
                    $context['theme'] = $theme;

                    return $wrapper->renderBlock($blockName, $context);
                }
            }

            $blockNameHierarchy[] = $blockName;
        }

        throw new LogicException(sprintf(
            'Unable to render any of the following blocks: "%s" as none of them is supported by any of the following themes: "%s".',
            implode('", "', array_reverse($blockNameHierarchy)),
            implode('", "', $dataTable->vars['themes'])
        ));
    }

    /**
     * @deprecated no replacement available
     *
     * @param array<string, mixed> $dataTableVariables
     * @param array<string, mixed> $formVariables
     *
     * @throws TwigException|\Throwable
     */
    public function renderDataTableFormAware(Environment $environment, DataTableView $view, FormView $formView, array $dataTableVariables = [], array $formVariables = []): string
    {
        return $this->renderBlock(
            environment: $environment,
            dataTable: $this->getDecoratedDataTableView($view, $dataTableVariables),
            blockName: 'kreyu_data_table_form_aware',
            context: array_merge($view->vars, $dataTableVariables, ['form' => $formView, 'form_variables' => $formVariables]),
        );
    }

    /**
     * @deprecated no replacement available
     */
    private function getDecoratedDataTableView(DataTableView $view, array $vars = []): DataTableView
    {
        if (!empty($themes = $vars['themes'] ?? [])) {
            if (!is_array($themes)) {
                throw new RuntimeError('The "themes" option passed in the template must be an array.');
            }

            $view->vars['themes'] = $themes;
        }

        return $view;
    }
}
