<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Extension\DataTableTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Type\DataTableTypeInterface;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeFactoryInterface;

interface DataTableFactoryBuilderInterface
{
    public function setResolvedTypeFactory(ResolvedDataTableTypeFactoryInterface $resolvedTypeFactory): static;

    public function setProxyQueryFactory(?ProxyQueryFactoryInterface $proxyQueryFactory): static;

    public function addExtension(DataTableExtensionInterface $extension): static;

    /**
     * @param array<DataTableExtensionInterface> $extensions
     */
    public function addExtensions(array $extensions): static;

    public function addType(DataTableTypeInterface $type): static;

    /**
     * @param array<DataTableTypeInterface> $types
     */
    public function addTypes(array $types): static;

    public function addTypeExtension(DataTableTypeExtensionInterface $typeExtension): static;

    /**
     * @param array<DataTableTypeExtensionInterface> $typeExtensions
     */
    public function addTypeExtensions(array $typeExtensions): static;

    public function getDataTableFactory(): DataTableFactoryInterface;
}
