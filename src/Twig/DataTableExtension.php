<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Twig;

use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnHeaderView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnSortUrlGeneratorInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Filter\FilterClearUrlGeneratorInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterView;
use Kreyu\Bundle\DataTableBundle\HeaderRowView;
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
        private readonly ColumnSortUrlGeneratorInterface $columnSortUrlGenerator,
        private readonly FilterClearUrlGeneratorInterface $filterClearUrlGenerator,
    ) {
    }

    public function getFunctions(): array
    {
        $definitions = [
            'data_table' => $this->renderDataTable(...),
            'data_table_form_aware' => $this->renderDataTableFormAware(...),
            'data_table_table' => $this->renderDataTableTable(...),
            'data_table_action_bar' => $this->renderDataTableActionBar(...),
            'data_table_header_row' => $this->renderHeaderRow(...),
            'data_table_value_row' => $this->renderValueRow(...),
            'data_table_column_label' => $this->renderColumnLabel(...),
            'data_table_column_header' => $this->renderColumnHeader(...),
            'data_table_column_value' => $this->renderColumnValue(...),
            'data_table_action' => $this->renderAction(...),
            'data_table_pagination' => $this->renderPagination(...),
            'data_table_filters_form' => $this->renderFiltersForm(...),
            'data_table_personalization_form' => $this->renderPersonalizationForm(...),
            'data_table_export_form' => $this->renderExportForm(...),
        ];

        $functions = [
            new TwigFunction('data_table_filter_clear_url', $this->generateFilterClearUrl(...)),
            new TwigFunction('data_table_column_sort_url', $this->generateColumnSortUrl(...)),
        ];

        foreach ($definitions as $name => $callable) {
            $functions[] = new TwigFunction($name, $callable, [
                'needs_environment' => true,
                'is_safe' => ['html'],
            ]);
        }

        return $functions;
    }

    /**
     * @param array<string, mixed> $variables
     *
     * @throws TwigException|\Throwable
     */
    public function renderDataTable(Environment $environment, DataTableView $view, array $variables = []): string
    {
        return $this->renderBlock(
            environment: $environment,
            dataTable: $this->getDecoratedDataTable($view, $variables),
            blockName: 'kreyu_data_table',
            context: array_merge($view->vars, $variables),
        );
    }

    /**
     * @param array<string, mixed> $dataTableVariables
     * @param array<string, mixed> $formVariables
     *
     * @throws TwigException|\Throwable
     */
    public function renderDataTableFormAware(Environment $environment, DataTableView $view, FormView $formView, array $dataTableVariables = [], array $formVariables = []): string
    {
        return $this->renderBlock(
            environment: $environment,
            dataTable: $this->getDecoratedDataTable($view, $dataTableVariables),
            blockName: 'kreyu_data_table_form_aware',
            context: array_merge($view->vars, $dataTableVariables, ['form' => $formView, 'form_variables' => $formVariables]),
        );
    }

    /**
     * @param array<string, mixed> $variables
     *
     * @throws TwigException|\Throwable
     */
    public function renderDataTableTable(Environment $environment, DataTableView $view, array $variables = []): string
    {
        return $this->renderBlock(
            environment: $environment,
            dataTable: $this->getDecoratedDataTable($view, $variables),
            blockName: 'kreyu_data_table_table',
            context: array_merge($view->vars, $variables),
        );
    }

    /**
     * @param array<string, mixed> $variables
     *
     * @throws TwigException|\Throwable
     */
    public function renderDataTableActionBar(Environment $environment, DataTableView $view, array $variables = []): string
    {
        return $this->renderBlock(
            environment: $environment,
            dataTable: $this->getDecoratedDataTable($view, $variables),
            blockName: 'kreyu_data_table_action_bar',
            context: array_merge($view->vars, $variables),
        );
    }

    /**
     * @param array<string, mixed> $variables
     *
     * @throws TwigException|\Throwable
     */
    public function renderHeaderRow(Environment $environment, HeaderRowView $view, array $variables = []): string
    {
        return $this->renderBlock(
            environment: $environment,
            dataTable: $this->getDecoratedDataTable($view->parent, $variables),
            blockName: 'kreyu_data_table_header_row',
            context: array_merge($view->vars, $variables),
        );
    }

    /**
     * @param array<string, mixed> $variables
     *
     * @throws TwigException|\Throwable
     */
    public function renderValueRow(Environment $environment, ValueRowView $view, array $variables = []): string
    {
        return $this->renderBlock(
            environment: $environment,
            dataTable: $this->getDecoratedDataTable($view->parent, $variables),
            blockName: 'kreyu_data_table_value_row',
            context: array_merge($view->vars, $variables),
        );
    }

    /**
     * @param array<string, mixed> $variables
     *
     * @throws TwigException|\Throwable
     */
    public function renderColumnLabel(Environment $environment, ColumnHeaderView $view, array $variables = []): string
    {
        return $this->renderBlock(
            environment: $environment,
            dataTable: $this->getDecoratedDataTable($view->getDataTable(), $variables),
            blockName: 'kreyu_data_table_column_label',
            context: array_merge($view->vars, $variables),
        );
    }

    /**
     * @param array<string, mixed> $variables
     *
     * @throws TwigException|\Throwable
     */
    public function renderColumnHeader(Environment $environment, ColumnHeaderView $view, array $variables = []): string
    {
        return $this->renderBlock(
            environment: $environment,
            dataTable: $this->getDecoratedDataTable($view->getDataTable(), $variables),
            blockName: 'kreyu_data_table_column_header',
            context: $this->getDecoratedViewContext($environment, $view, $variables, 'column', 'header'),
        );
    }

    /**
     * @param array<string, mixed> $variables
     *
     * @throws TwigException|\Throwable
     */
    public function renderColumnValue(Environment $environment, ColumnValueView $view, array $variables = []): string
    {
        return $this->renderBlock(
            environment: $environment,
            dataTable: $this->getDecoratedDataTable($view->getDataTable(), $variables),
            blockName: 'kreyu_data_table_column_value',
            context: $this->getDecoratedViewContext($environment, $view, $variables, 'column', 'value'),
        );
    }

    /**
     * @param array<string, mixed> $variables
     *
     * @throws TwigException|\Throwable
     */
    public function renderAction(Environment $environment, ActionView $view, array $variables = []): string
    {
        return $this->renderBlock(
            environment: $environment,
            dataTable: $this->getDecoratedDataTable($view->getDataTable(), $variables),
            blockName: 'kreyu_data_table_action',
            context: $this->getDecoratedViewContext($environment, $view, $variables, 'action', 'value'),
        );
    }

    /**
     * @param array<string, mixed> $variables
     *
     * @throws TwigException|\Throwable
     */
    public function renderPagination(Environment $environment, DataTableView|PaginationView $view, array $variables = []): string
    {
        if ($view instanceof DataTableView) {
            $view = $view->vars['pagination'];
        }

        return $this->renderBlock(
            environment: $environment,
            dataTable: $this->getDecoratedDataTable($view->parent, $variables),
            blockName: 'kreyu_data_table_pagination',
            context: array_merge($view->vars, $variables),
        );
    }

    /**
     * @throws TwigException|\Throwable
     */
    public function renderFiltersForm(Environment $environment, FormInterface|FormView $form, array $variables = []): string
    {
        if ($form instanceof FormInterface) {
            $form = $form->createView();
        }

        return $this->renderBlock(
            environment: $environment,
            dataTable: $this->getDecoratedDataTable($form->vars['data_table_view'], $variables),
            blockName: 'kreyu_data_table_filters_form',
            context: [
                'form' => $form,
            ],
        );
    }

    /**
     * @throws TwigException|\Throwable
     */
    public function renderPersonalizationForm(Environment $environment, FormInterface|FormView $form, array $variables = []): string
    {
        if ($form instanceof FormInterface) {
            $form = $form->createView();
        }

        return $this->renderBlock(
            environment: $environment,
            dataTable: $this->getDecoratedDataTable($form->vars['data_table_view'], $variables),
            blockName: 'kreyu_data_table_personalization_form',
            context: [
                'form' => $form,
            ],
        );
    }

    /**
     * @throws TwigException|\Throwable
     */
    public function renderExportForm(Environment $environment, FormInterface|FormView $form, array $variables = []): string
    {
        if ($form instanceof FormInterface) {
            $form = $form->createView();
        }

        return $this->renderBlock(
            environment: $environment,
            dataTable: $this->getDecoratedDataTable($form->vars['data_table_view'], $variables),
            blockName: 'kreyu_data_table_export_form',
            context: [
                'form' => $form,
            ],
        );
    }

    public function generateFilterClearUrl(FilterView|array $filterViews): string
    {
        if ($filterViews instanceof FilterView) {
            $filterViews = [$filterViews];
        }

        return $this->filterClearUrlGenerator->generate(...$filterViews);
    }

    public function generateColumnSortUrl(ColumnHeaderView|array $columnHeaderViews): string
    {
        if ($columnHeaderViews instanceof ColumnHeaderView) {
            $columnHeaderViews = [$columnHeaderViews];
        }

        return $this->columnSortUrlGenerator->generate(...$columnHeaderViews);
    }

    /**
     * @param array<string, mixed> $context
     *
     * @throws TwigException|\Throwable
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

        throw new RuntimeError(sprintf('Block "%s" does not exist on any of the configured data table themes', $blockName));
    }

    /**
     * @param array<string, mixed> $variables
     *
     * @return array<string, mixed>
     *
     * @throws TwigException|\Throwable
     */
    private function getDecoratedViewContext(Environment $environment, ColumnHeaderView|ColumnValueView|ActionView $view, array $variables, string $prefix, string $suffix): array
    {
        $dataTable = $view->getDataTable();

        $context = array_merge($view->vars, $variables);
        $context['block_name'] = $prefix.'_'.$suffix;

        foreach ($view->vars['block_prefixes'] as $blockPrefix) {
            $blockName = $blockPrefix.'_'.$suffix;

            if ($prefix !== $blockPrefix) {
                $blockName = $prefix.'_'.$blockName;
            }

            foreach ($dataTable->vars['themes'] as $theme) {
                $wrapper = $environment->load($theme);

                if ($wrapper->hasBlock($blockName, $context)) {
                    $context['block_name'] = $blockName;
                    $context['block_theme'] = $theme;

                    break 2;
                }
            }
        }

        return $context;
    }

    private function getDecoratedDataTable(DataTableView $view, array $variables = []): DataTableView
    {
        if (!empty($themes = $variables['themes'] ?? [])) {
            if (!is_array($themes)) {
                throw new RuntimeError('The "themes" option passed in the template must be an array.');
            }

            $view->vars['themes'] = $themes;
        }

        return $view;
    }
}
