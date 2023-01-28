<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Twig;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnView;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\HeadersView;
use Kreyu\Bundle\DataTableBundle\RowView;
use Symfony\Component\Form\FormInterface;
use Twig\Environment;
use Twig\Error\Error as TwigException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DataTableExtension extends AbstractExtension
{
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

    public function renderHeaders(Environment $environment, HeadersView $view): string
    {
        return $environment->render('@KreyuDataTable/headers.html.twig', $view->vars);
    }

    public function renderRow(Environment $environment, RowView $view): string
    {
        return $environment->render('@KreyuDataTable/row.html.twig', $view->vars);
    }

    /**
     * @throws TwigException
     */
    public function renderDataTable(Environment $environment, DataTableView $view): string
    {
        dump($view);

        return $environment->render('@KreyuDataTable/data_table.html.twig', $view->vars);
    }

    /**
     * @throws TwigException
     */
    public function renderColumnLabel(Environment $environment, DataTableInterface|DataTableView $dataTable, ColumnInterface|ColumnView $column): string
    {
        if ($column instanceof ColumnInterface) {
            if ($dataTable instanceof DataTableInterface) {
                $dataTable = $dataTable->createView();
            }

            $column = $column->createView(parent: $dataTable);
        }

        return $environment->render('@KreyuDataTable/column_label.html.twig', [
            'column' => $column,
        ]);
    }

    /**
     * @throws TwigException
     */
    public function renderColumnHeader(Environment $environment, ColumnView $view): string
    {
        return $environment->render('@KreyuDataTable/column_header.html.twig', $view->vars);
    }

    /**
     * @throws TwigException
     */
    public function renderColumnValue(Environment $environment, ColumnView $view): string
    {
        return $environment->render('@KreyuDataTable/column_value.html.twig', $view->vars);
    }

    /**
     * @throws TwigException
     */
    public function renderFiltersForm(Environment $environment, FormInterface $form): string
    {
        return $environment->render('@KreyuDataTable/filters_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws TwigException
     */
    public function renderPersonalizationForm(Environment $environment, FormInterface $form): string
    {
        return $environment->render('@KreyuDataTable/personalization_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws TwigException
     */
    public function renderPagination(Environment $environment, DataTableViewInterface $dataTable): string
    {
        return $environment->render('@KreyuDataTable/pagination.html.twig', [
            'data_table' => $dataTable,
        ]);
    }
}
