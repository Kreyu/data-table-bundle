<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\HeaderRowView;
use Kreyu\Bundle\DataTableBundle\ValueRowView;

class Column implements ColumnInterface
{
    private mixed $rowIndex = null;
    private mixed $rowData = null;

    public function __construct(
        private string $name,
        private ResolvedColumnTypeInterface $type,
        private array $options = [],
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): ResolvedColumnTypeInterface
    {
        return $this->type;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getRowIndex(): mixed
    {
        return $this->rowIndex;
    }

    public function setRowIndex(mixed $rowIndex): void
    {
        $this->rowIndex = $rowIndex;
    }

    public function getRowData(): mixed
    {
        return $this->rowData;
    }

    public function setRowData(mixed $rowData): void
    {
        $this->rowData = $rowData;
    }

    public function createHeaderView(HeaderRowView $parent = null): ColumnHeaderView
    {
        $view = $this->type->createHeaderView($this, $parent);

        $this->type->buildHeaderView($view, $this, $this->options);

        return $view;
    }

    public function createValueView(ValueRowView $parent = null): ColumnValueView
    {
        $view = $this->type->createValueView($this, $parent);

        $this->type->buildValueView($view, $this, $this->options);

        return $view;
    }

    public function getBlockPrefixes(): array
    {
        $type = $this->getType();

        $blockPrefixes = [
            $type->getBlockPrefix(),
        ];

        while (null !== $type->getParent()) {
            $blockPrefixes[] = ($type = $type->getParent())->getBlockPrefix();
        }

        return $blockPrefixes;
    }
}
