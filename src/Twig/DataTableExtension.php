<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Twig;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnView;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\HeadersView;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationView;
use Kreyu\Bundle\DataTableBundle\RowView;
use Symfony\Component\Form\FormInterface;
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
                'data_table_headers',
                [$this, 'renderHeaders'],
                ['needs_environment' => true, 'is_safe' => ['html']],
            ),
            new TwigFunction(
                'data_table_row',
                [$this, 'renderRow'],
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
    public function renderHeaders(Environment $environment, HeadersView $view): string
    {
        return $this->renderBlock($environment, 'kreyu_data_table_header_row', $view->vars);
    }

    /**
     * @throws TwigException|Throwable
     */
    public function renderColumnHeader(Environment $environment, ColumnView $view): string
    {
        return $this->renderBlock($environment, 'kreyu_data_table_column_header', $view->vars);
    }

    /**
     * @throws TwigException|Throwable
     */
    public function renderRow(Environment $environment, RowView $view): string
    {
        return $this->renderBlock($environment, 'kreyu_data_table_value_row', $view->vars);
    }

    /**
     * @throws TwigException|Throwable
     */
    public function renderColumnLabel(Environment $environment, ColumnView $view): string
    {
        return $this->renderBlock($environment, 'kreyu_data_table_column_label', $view->vars);
    }

    /**
     * @throws TwigException|Throwable
     */
    public function renderColumnValue(Environment $environment, ColumnView $view): string
    {
        return $this->renderBlock($environment, 'kreyu_data_table_column_value', $view->vars);
    }

    /**
     * @throws TwigException|Throwable
     */
    public function renderDataTable(Environment $environment, DataTableView $view): string
    {
        return $this->renderBlock($environment, 'kreyu_data_table', $view->vars);
    }

    /**
     * @throws TwigException|Throwable
     */
    public function renderFiltersForm(Environment $environment, FormInterface $form): string
    {
        return $this->renderBlock($environment, 'kreyu_data_table_filters_form', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws TwigException|Throwable
     */
    public function renderPersonalizationForm(Environment $environment, FormInterface $form): string
    {
        return $this->renderBlock($environment, 'kreyu_data_table_personalization_form', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws TwigException|Throwable
     */
    public function renderPagination(Environment $environment, PaginationView $view): string
    {
        return $this->renderBlock($environment, 'kreyu_data_table_pagination', $view->vars);
    }
}
