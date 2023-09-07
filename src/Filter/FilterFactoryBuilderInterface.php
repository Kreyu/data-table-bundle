<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\Filter\Extension\FilterExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Extension\FilterTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeFactoryInterface;

interface FilterFactoryBuilderInterface
{
    public function setResolvedTypeFactory(ResolvedFilterTypeFactoryInterface $resolvedTypeFactory): static;

    public function addExtension(FilterExtensionInterface $extension): static;

    /**
     * @param array<FilterExtensionInterface> $extensions
     */
    public function addExtensions(array $extensions): static;

    public function addType(FilterTypeInterface $type): static;

    /**
     * @param array<FilterTypeInterface> $types
     */
    public function addTypes(array $types): static;

    public function addTypeExtension(FilterTypeExtensionInterface $typeExtension): static;

    /**
     * @param array<FilterTypeExtensionInterface> $typeExtensions
     */
    public function addTypeExtensions(array $typeExtensions): static;

    public function getFilterFactory(): FilterFactoryInterface;
}
