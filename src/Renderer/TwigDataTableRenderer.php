<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Renderer;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Twig\Environment;

class TwigDataTableRenderer implements DataTableRendererInterface
{
    public function __construct(
        private Environment $environment,
    ) {
    }

    public function renderColumnHeader(DataTableView $dataTableView, ColumnInterface $column): string
    {
        $columnView = $column->createView($dataTableView);

        return $this->environment->render('@KreyuDataTable/column_header.html.twig', [

        ]);
    }

    public function renderHeaders(): string
    {
        $columnViews = [];

        foreach ($this->getColumns() as $column) {
            $columnViews[] = $column->createView($this->dataTable);
        }
    }

    public function renderRow(): string
    {

    }

    /**
     * @return array<ColumnInterface>
     */
    private function getColumns(): array
    {
        return $this->dataTable->vars['columns'];
    }
}