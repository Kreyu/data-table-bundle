<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Twig;

use Kreyu\Bundle\DataTableBundle\Column\ColumnView;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\HeadersRowView;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationView;
use Kreyu\Bundle\DataTableBundle\ValuesRowView;
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
        private array $themes,
    ) {
    }

    public function getFunctions(): array
    {
        $definitions = [
            'data_table' => $this->renderDataTable(...),
            'data_table_form_aware' => $this->renderDataTableFormAware(...),
            'data_table_table' => $this->renderDataTableTable(...),
            'data_table_action_bar' => $this->renderDataTableActionBar(...),
            'data_table_headers_row' => $this->renderHeadersRow(...),
            'data_table_values_row' => $this->renderValuesRow(...),
            'data_table_column_label' => $this->renderColumnLabel(...),
            'data_table_column_header' => $this->renderColumnHeader(...),
            'data_table_column_value' => $this->renderColumnValue(...),
            'data_table_pagination' => $this->renderPagination(...),
            'data_table_filters_form' => $this->renderFiltersForm(...),
            'data_table_personalization_form' => $this->renderPersonalizationForm(...),
            'data_table_export_form' => $this->renderExportForm(...),
        ];

        $functions = [];

        foreach ($definitions as $name => $callable) {
            $functions[] = new TwigFunction($name, $callable, [
                'needs_environment' => true,
                'is_safe' => ['html'],
            ]);
        }

        return $functions;
    }

    /**
     * @throws TwigException|\Throwable
     */
    public function renderDataTable(Environment $environment, DataTableView $view, array $variables = []): string
    {
        return $this->renderBlock(
            environment: $environment,
            blockName: 'kreyu_data_table',
            context: array_merge($view->vars, $variables),
        );
    }

    /**
     * @throws TwigException|\Throwable
     */
    public function renderDataTableFormAware(Environment $environment, DataTableView $view, FormView $formView, array $dataTableVariables = [], array $formVariables = []): string
    {
        return $this->renderBlock(
            environment: $environment,
            blockName: 'kreyu_data_table_form_aware',
            context: array_merge($view->vars, $dataTableVariables, ['form' => $formView, 'form_variables' => $formVariables]),
        );
    }

    /**
     * @throws TwigException|\Throwable
     */
    public function renderDataTableTable(Environment $environment, DataTableView $view, array $variables = []): string
    {
        return $this->renderBlock(
            environment: $environment,
            blockName: 'kreyu_data_table_table',
            context: array_merge($view->vars, $variables),
        );
    }

    /**
     * @throws TwigException|\Throwable
     */
    public function renderDataTableActionBar(Environment $environment, DataTableView $view, array $variables = []): string
    {
        return $this->renderBlock(
            environment: $environment,
            blockName: 'kreyu_data_table_action_bar',
            context: array_merge($view->vars, $variables),
        );
    }

    /**
     * @throws TwigException|\Throwable
     */
    public function renderHeadersRow(Environment $environment, HeadersRowView $view, array $variables = []): string
    {
        return $this->renderBlock(
            environment: $environment,
            blockName: 'kreyu_data_table_headers_row',
            context: array_merge($view->vars, $variables),
        );
    }

    /**
     * @throws TwigException|\Throwable
     */
    public function renderValuesRow(Environment $environment, ValuesRowView $view, array $variables = []): string
    {
        return $this->renderBlock(
            environment: $environment,
            blockName: 'kreyu_data_table_values_row',
            context: array_merge($view->vars, $variables),
        );
    }

    /**
     * @throws TwigException|\Throwable
     */
    public function renderColumnLabel(Environment $environment, ColumnView $view, array $variables = []): string
    {
        return $this->renderBlock(
            environment: $environment,
            blockName: 'kreyu_data_table_column_label',
            context: array_merge($view->vars, $variables),
        );
    }

    /**
     * @throws TwigException|\Throwable
     */
    public function renderColumnHeader(Environment $environment, ColumnView $view, array $variables = []): string
    {
        return $this->renderBlock(
            environment: $environment,
            blockName: 'kreyu_data_table_column_header',
            context: array_merge($view->vars, $variables),
        );
    }

    /**
     * @throws TwigException|\Throwable
     */
    public function renderColumnValue(Environment $environment, ColumnView $view, array $variables = []): string
    {
        return $this->renderBlock(
            environment: $environment,
            blockName: 'kreyu_data_table_column_value',
            context: array_merge($view->vars, $variables),
        );
    }

    /**
     * @throws TwigException|\Throwable
     */
    public function renderPagination(Environment $environment, DataTableView|PaginationView $view, array $variables = []): string
    {
        if ($view instanceof DataTableView) {
            $view = $view->vars['pagination'];
        }

        return $this->renderBlock(
            environment: $environment,
            blockName: 'kreyu_data_table_pagination',
            context: array_merge($view->vars, $variables),
        );
    }

    /**
     * @throws TwigException|\Throwable
     */
    public function renderFiltersForm(Environment $environment, FormInterface|FormView $form): string
    {
        if ($form instanceof FormInterface) {
            $form = $form->createView();
        }

        return $this->renderBlock(
            environment: $environment,
            blockName: 'kreyu_data_table_filters_form',
            context: [
                'form' => $form,
            ],
        );
    }

    /**
     * @throws TwigException|\Throwable
     */
    public function renderPersonalizationForm(Environment $environment, FormInterface|FormView $form): string
    {
        if ($form instanceof FormInterface) {
            $form = $form->createView();
        }

        return $this->renderBlock(
            environment: $environment,
            blockName: 'kreyu_data_table_personalization_form',
            context: [
                'form' => $form,
            ],
        );
    }

    /**
     * @throws TwigException|\Throwable
     */
    public function renderExportForm(Environment $environment, FormInterface|FormView $form): string
    {
        if ($form instanceof FormInterface) {
            $form = $form->createView();
        }

        return $this->renderBlock(
            environment: $environment,
            blockName: 'kreyu_data_table_export_form',
            context: [
                'form' => $form,
            ],
        );
    }

    /**
     * @throws TwigException|\Throwable
     */
    private function renderBlock(Environment $environment, string $blockName, array $context = []): string
    {
        foreach ($this->themes as $theme) {
            $wrapper = $environment->load($theme);

            if ($wrapper->hasBlock($blockName, $context)) {
                $context['theme'] = $theme;

                return $wrapper->renderBlock($blockName, $context);
            }
        }

        throw new RuntimeError(sprintf('Block "%s" does not exist on any of the configured data table themes', $blockName));
    }
}
