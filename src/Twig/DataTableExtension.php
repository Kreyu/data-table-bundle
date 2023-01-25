<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Twig;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnView;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Symfony\Component\PropertyAccess\PropertyAccess;
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
     * @throws TwigException
     */
    public function renderDataTable(Environment $environment, DataTableInterface|DataTableView $dataTable): string
    {
        if ($dataTable instanceof DataTableInterface) {
            $dataTable = $dataTable->createView();
        }

        return $environment->render('@KreyuDataTable/data_table.html.twig', [
            'data_table' => $dataTable,
        ]);
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
    public function renderColumnHeader(Environment $environment, DataTableInterface|DataTableView $dataTable, ColumnInterface|ColumnView $column): string
    {
        if ($column instanceof ColumnInterface) {
            if ($dataTable instanceof DataTableInterface) {
                $dataTable = $dataTable->createView();
            }

            $column = $column->createView(parent: $dataTable);
        }

        return $environment->render('@KreyuDataTable/column_header.html.twig', [
            'column' => $column,
        ]);
    }

    /**
     * @throws TwigException
     */
    public function renderColumnValue(Environment $environment, DataTableInterface|DataTableView $dataTable, ColumnInterface|ColumnView $column, mixed $data): string
    {
        if ($column instanceof ColumnInterface) {
            $column->setData($data);

            if ($dataTable instanceof DataTableInterface) {
                $dataTable = $dataTable->createView();
            }

            $column = $column->createView($data, $dataTable);
        }

        return $environment->render('@KreyuDataTable/column_value.html.twig', [
            'column' => $column,
        ]);
    }

    /**
     * @throws TwigException
     */
    public function renderFiltersForm(Environment $environment, DataTableViewInterface $dataTable): string
    {
        return $environment->render('@KreyuDataTable/filters_form.html.twig', [
            'data_table' => $dataTable,
        ]);
    }

    /**
     * @throws TwigException
     */
    public function renderPersonalizationForm(Environment $environment, DataTableViewInterface $dataTable): string
    {
        return $environment->render('@KreyuDataTable/personalization_form.html.twig', [
            'data_table' => $dataTable,
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