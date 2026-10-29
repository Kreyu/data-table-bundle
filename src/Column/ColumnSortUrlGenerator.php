<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Request\AbstractUrlGenerator;

class ColumnSortUrlGenerator extends AbstractUrlGenerator implements ColumnSortUrlGeneratorInterface
{
    public function generate(DataTableView $dataTableView, ColumnHeaderView ...$columnHeaderViews): string
    {
        $parameters = $this->getUrlParameters($dataTableView);

        /** @var string $sortParameterName */
        $sortParameterName = $dataTableView->vars['sort_parameter_name'];

        unset($parameters[$sortParameterName]);

        foreach ($columnHeaderViews as $columnHeaderView) {
            $parameters = array_replace_recursive($parameters, $this->getColumnSortQueryParameters($columnHeaderView));
        }

        $parameters = [...$parameters, ...$this->getFirstPageParameters($dataTableView)];

        return $this->generateUrl($parameters);
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function getColumnSortQueryParameters(ColumnHeaderView $columnHeaderView): array
    {
        /** @var string $sortParameterName */
        $sortParameterName = $columnHeaderView->getDataTable()->vars['sort_parameter_name'];

        /** @var string $columnName */
        $columnName = $columnHeaderView->vars['name'];

        return [
            $sortParameterName => [
                $columnName => $this->getOppositeSortDirection($columnHeaderView),
            ],
        ];
    }

    private function getOppositeSortDirection(ColumnHeaderView $columnHeaderView): string
    {
        $sortDirection = mb_strtolower((string) $columnHeaderView->vars['sort_direction']);

        /** @var bool $isSortingClearable */
        $isSortingClearable = $columnHeaderView->getDataTable()->vars['sorting_clearable'] ?? true;

        return match ($sortDirection) {
            'asc' => 'desc',
            'desc' => $isSortingClearable ? 'none' : 'asc',
            default => 'asc',
        };
    }
}
