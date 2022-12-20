<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Twig;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\Renderer\ColumnRendererInterface;
use Kreyu\Bundle\DataTableBundle\View\DataTableViewInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ColumnRendererExtension extends AbstractExtension
{
    public function __construct(
        private readonly ColumnRendererInterface $columnRenderer,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'render_data_table_column_header',
                [$this, 'renderColumnHeader'],
                ['is_safe' => ['html']],
            ),
            new TwigFunction(
                'render_data_table_column_value',
                [$this, 'renderColumnValue'],
                ['is_safe' => ['html']],
            ),
        ];
    }

    public function renderColumnHeader(DataTableViewInterface $dataTable, ColumnInterface $column): string
    {
        return $this->columnRenderer->renderHeader($dataTable, $column);
    }

    public function renderColumnValue(DataTableViewInterface $dataTable, ColumnInterface $column, mixed $value): string
    {
        return $this->columnRenderer->renderValue($dataTable, $column, $value);
    }
}
