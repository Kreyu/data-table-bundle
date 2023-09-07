<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeInterface;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

interface ColumnConfigBuilderInterface extends ColumnConfigInterface
{
    /**
     * @deprecated since 0.14.0, provide the name using the factory {@see ColumnFactoryInterface} "named" methods instead
     */
    public function setName(string $name): static;

    public function setType(ResolvedColumnTypeInterface $type): static;

    /**
     * @deprecated since 0.14.0, modifying the options dynamically will be removed as it creates unexpected behaviors
     */
    public function setOptions(array $options): static;

    /**
     * @deprecated since 0.14.0, modifying the options dynamically will be removed as it creates unexpected behaviors
     */
    public function setOption(string $name, mixed $value): static;

    public function setAttributes(array $attributes): static;

    public function setAttribute(string $name, mixed $value): static;

    public function setPropertyPath(null|string|PropertyPathInterface $propertyPath): static;

    public function setSortPropertyPath(null|string|PropertyPathInterface $sortPropertyPath): static;

    public function setSortable(bool $sortable): static;

    public function setExportable(bool $exportable): static;

    public function setPersonalizable(bool $personalizable): static;

    public function getColumnConfig(): ColumnConfigInterface;
}
