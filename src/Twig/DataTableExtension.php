<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Twig;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnView;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\HeadersRowView;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationView;
use Kreyu\Bundle\DataTableBundle\ValuesRowView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Throwable;
use Twig\Environment;
use Twig\Error\Error as TwigException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DataTableExtension extends AbstractExtension
{
    public function __construct(
        private string $themeTemplate
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'data_table',
                [$this, 'renderDataTable'],
                ['needs_environment' => true, 'is_safe' => ['html']],
            ),
            new TwigFunction(
                'data_table_headers_row',
                [$this, 'renderHeadersRow'],
                ['needs_environment' => true, 'is_safe' => ['html']],
            ),
            new TwigFunction(
                'data_table_values_row',
                [$this, 'renderValuesRow'],
                ['needs_environment' => true, 'is_safe' => ['html']],
            ),
            new TwigFunction(
                'data_table_column_label',
                [$this, 'renderColumnLabel'],
                ['needs_environment' => true, 'is_safe' => ['html']],
            ),
            new TwigFunction(
                'data_table_column_header',
                [$this, 'renderColumnHeader'],
                ['needs_environment' => true, 'is_safe' => ['html']],
            ),
            new TwigFunction(
                'data_table_column_value',
                [$this, 'renderColumnValue'],
                ['needs_environment' => true, 'is_safe' => ['html']],
            ),
            new TwigFunction(
                'data_table_filters_form',
                [$this, 'renderFiltersForm'],
                ['needs_environment' => true, 'is_safe' => ['html']],
            ),
            new TwigFunction(
                'data_table_personalization_form',
                [$this, 'renderPersonalizationForm'],
                ['needs_environment' => true, 'is_safe' => ['html']],
            ),
            new TwigFunction(
                'data_table_export_form',
                [$this, 'renderExportForm'],
                ['needs_environment' => true, 'is_safe' => ['html']],
            ),
            new TwigFunction(
                'data_table_pagination',
                [$this, 'renderPagination'],
                ['needs_environment' => true, 'is_safe' => ['html']],
            ),
        ];
    }

    /**
     * @throws TwigException|Throwable
     */
    private function renderBlock(Environment $environment, string $blockName, array $context = []): string
    {
        return $environment
            ->load($this->themeTemplate)
            ->renderBlock($blockName, $context)
        ;
    }

    /**
     * @throws TwigException|Throwable
     */
    public function renderHeadersRow(Environment $environment, HeadersRowView $view, array $variables = []): string
    {
        return $this->renderBlock($environment, 'kreyu_data_table_headers_row', array_merge($view->vars, $variables));
    }

    /**
     * @throws TwigException|Throwable
     */
    public function renderValuesRow(Environment $environment, ValuesRowView $view, array $variables = []): string
    {
        return $this->renderBlock($environment, 'kreyu_data_table_values_row', array_merge($view->vars, $variables));
    }

    /**
     * @throws TwigException|Throwable
     */
    public function renderColumnHeader(Environment $environment, ColumnView $view, array $variables = []): string
    {
        return $this->renderBlock($environment, 'kreyu_data_table_column_header', array_merge($view->vars, $variables));
    }

    /**
     * @throws TwigException|Throwable
     */
    public function renderColumnLabel(Environment $environment, ColumnView $view, array $variables = []): string
    {
        return $this->renderBlock($environment, 'kreyu_data_table_column_label', array_merge($view->vars, $variables));
    }

    /**
     * @throws TwigException|Throwable
     */
    public function renderColumnValue(Environment $environment, ColumnView $view, array $variables = []): string
    {
        return $this->renderBlock($environment, 'kreyu_data_table_column_value', array_merge($view->vars, $variables));
    }

    /**
     * @throws TwigException|Throwable
     */
    public function renderDataTable(Environment $environment, DataTableView $view, array $variables = []): string
    {
        return $this->renderBlock($environment, 'kreyu_data_table', array_merge($view->vars, $variables));
    }

    /**
     * @throws TwigException|Throwable
     */
    public function renderFiltersForm(Environment $environment, FormInterface|FormView $form): string
    {
        if ($form instanceof FormInterface) {
            $form = $form->createView();
        }

        return $this->renderBlock($environment, 'kreyu_data_table_filters_form', [
            'form' => $form,
        ]);
    }

    /**
     * @throws TwigException|Throwable
     */
    public function renderPersonalizationForm(Environment $environment, FormInterface|FormView $form): string
    {
        if ($form instanceof FormInterface) {
            $form = $form->createView();
        }

        return $this->renderBlock($environment, 'kreyu_data_table_personalization_form', [
            'form' => $form,
        ]);
    }

    /**
     * @throws TwigException|Throwable
     */
    public function renderExportForm(Environment $environment, FormInterface|FormView $form): string
    {
        if ($form instanceof FormInterface) {
            $form = $form->createView();
        }

        return $this->renderBlock($environment, 'kreyu_data_table_export_form', [
            'form' => $form,
        ]);
    }

    /**
     * @throws TwigException|Throwable
     */
    public function renderPagination(Environment $environment, PaginationView $view, array $variables = []): string
    {
        return $this->renderBlock($environment, 'kreyu_data_table_pagination', array_merge($view->vars, $variables));
    }
}
