<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\Column\Extension\ColumnExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Column\Extension\ColumnTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeFactoryInterface;

interface ColumnFactoryBuilderInterface
{
    public function setResolvedTypeFactory(ResolvedColumnTypeFactoryInterface $resolvedTypeFactory): static;

    public function addExtension(ColumnExtensionInterface $extension): static;

    /**
     * @param array<ColumnExtensionInterface> $extensions
     */
    public function addExtensions(array $extensions): static;

    public function addType(ColumnTypeInterface $type): static;

    /**
     * @param array<ColumnTypeInterface> $types
     */
    public function addTypes(array $types): static;

    public function addTypeExtension(ColumnTypeExtensionInterface $typeExtension): static;

    /**
     * @param array<ColumnTypeExtensionInterface> $typeExtensions
     */
    public function addTypeExtensions(array $typeExtensions): static;

    public function getColumnFactory(): ColumnFactoryInterface;
}