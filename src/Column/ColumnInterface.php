<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;

interface ColumnInterface
{
    public function getName(): string;

    public function getType(): ResolvedColumnTypeInterface;

    public function getOptions(): array;

    public function getData(): mixed;

    public function setData(mixed $data): void;

    public function createView(DataTableView $parent = null): ColumnView;
}
