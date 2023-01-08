<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Twig;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\View\Factory\ColumnHeaderViewFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\View\Factory\ColumnValueViewFactoryInterface;
use Kreyu\Bundle\DataTableBundle\View\DataTableViewInterface;
use Twig\Environment;
use Twig\Error\Error as TwigException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DataTableExtension extends AbstractExtension
{
    public function __construct(
        private readonly ColumnHeaderViewFactoryInterface $columnHeaderViewFactory,
        private readonly ColumnValueViewFactoryInterface $columnValueViewFactory,
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
    public function renderDataTable(Environment $environment, DataTableViewInterface $dataTable): string
    {
        return $environment->render('@KreyuDataTable/data_table.html.twig', [
            'data_table' => $dataTable,
        ]);
    }

    /**
     * @throws TwigException
     */
    public function renderColumnHeader(Environment $environment, DataTableViewInterface $dataTable, ColumnInterface $column): string
    {
        $view = $this->columnHeaderViewFactory->create($dataTable, $column);

        return $environment->render('@KreyuDataTable/column_header.html.twig', $view->getVariables());
    }

    /**
     * @throws TwigException
     */
    public function renderColumnValue(Environment $environment, DataTableViewInterface $dataTable, ColumnInterface $column, mixed $value): string
    {
        $view = $this->columnValueViewFactory->create($dataTable, $column, $value);

        return $environment->render('@KreyuDataTable/column_value.html.twig', $view->getVariables());
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
