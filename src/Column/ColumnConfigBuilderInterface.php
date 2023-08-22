<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeInterface;

interface ColumnConfigBuilderInterface extends ColumnConfigInterface
{
    public function setName(string $name): static;

    public function setType(ResolvedColumnTypeInterface $type): static;

    public function setOptions(array $options): static;

    public function setOption(string $name, mixed $value): static;

    public function setAttributes(array $attributes): static;

    public function setAttribute(string $name, mixed $value = null): static;

    public function setSortable(bool $sortable): static;

    public function setExportable(bool $exportable): static;

    public function getColumnConfig(): ColumnConfigInterface;
}
