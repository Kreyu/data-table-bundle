<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Renderer;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\View\Factory\ColumnHeaderViewFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\View\Factory\ColumnValueViewFactoryInterface;
use Kreyu\Bundle\DataTableBundle\View\DataTableViewInterface;
use Twig\Environment;
use Twig\Error\Error as TwigException;

class HtmlColumnRenderer implements ColumnRendererInterface
{
    public function __construct(
        private readonly ColumnHeaderViewFactoryInterface $columnHeaderViewFactory,
        private readonly ColumnValueViewFactoryInterface $columnValueViewFactory,
        private readonly Environment $twig,
    ) {
    }

    /**
     * @throws TwigException
     */
    public function renderHeader(DataTableViewInterface $dataTable, ColumnInterface $column): string
    {
        $view = $this->columnHeaderViewFactory->create($dataTable, $column);

        return $this->twig->render(
            name: '@KreyuDataTable/column_header.html.twig',
            context: $view->getVariables(),
        );
    }

    /**
     * @throws TwigException
     */
    public function renderValue(DataTableViewInterface $dataTable, ColumnInterface $column, mixed $value): string
    {
        $view = $this->columnValueViewFactory->create($dataTable, $column, $value);

        return $this->twig->render(
            name: '@KreyuDataTable/column_value.html.twig',
            context: $view->getVariables(),
        );
    }
}
