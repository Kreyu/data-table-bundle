<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\HeaderRowView;
use Kreyu\Bundle\DataTableBundle\ValueRowView;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

interface ColumnInterface
{
    public function getName(): string;

    public function getConfig(): ColumnConfigInterface;

    public function getDataTable(): DataTableInterface;

    public function setDataTable(DataTableInterface $dataTable): static;

    public function getPropertyPath(): ?PropertyPathInterface;

    public function getSortPropertyPath(): ?PropertyPathInterface;

    public function createHeaderView(HeaderRowView $parent = null): ColumnHeaderView;

    public function createValueView(ValueRowView $parent = null): ColumnValueView;

    public function createExportHeaderView(HeaderRowView $parent = null): ColumnHeaderView;

    public function createExportValueView(ValueRowView $parent = null): ColumnValueView;

    public function getPriority(): int;

    public function setPriority(int $priority): static;

    public function isVisible(): bool;

    public function setVisible(bool $visible): static;
}
