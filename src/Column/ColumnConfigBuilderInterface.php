<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeInterface;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

interface ColumnConfigBuilderInterface extends ColumnConfigInterface
{
    public function setType(ResolvedColumnTypeInterface $type): static;

    public function setAttributes(array $attributes): static;

    public function setAttribute(string $name, mixed $value): static;

    public function setPropertyPath(string|PropertyPathInterface|null $propertyPath): static;

    public function setSortPropertyPath(string|PropertyPathInterface|null $sortPropertyPath): static;

    public function setSortable(bool $sortable): static;

    public function setExportable(bool $exportable): static;

    public function setPersonalizable(bool $personalizable): static;

    public function setPriority(int $priority): static;

    public function setVisible(bool $visible): static;

    public function setColumnFactory(ColumnFactoryInterface $columnFactory): static;

    public function getColumnConfig(): ColumnConfigInterface;
}
