<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Extension\DataTableExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Extension\DataTableTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Extension\PreloadedDataTableExtension;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;
use Kreyu\Bundle\DataTableBundle\Type\DataTableTypeInterface;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeFactory;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeFactoryInterface;

class DataTableFactoryBuilder implements DataTableFactoryBuilderInterface
{
    private ResolvedDataTableTypeFactoryInterface $resolvedTypeFactory;
    private ?ProxyQueryFactoryInterface $proxyQueryFactory = null;
    private array $extensions = [];
    private array $types = [];
    private array $typeExtensions = [];

    public function setResolvedTypeFactory(ResolvedDataTableTypeFactoryInterface $resolvedTypeFactory): static
    {
        $this->resolvedTypeFactory = $resolvedTypeFactory;

        return $this;
    }

    public function addExtension(DataTableExtensionInterface $extension): static
    {
        $this->extensions[] = $extension;

        return $this;
    }

    public function addExtensions(array $extensions): static
    {
        $this->extensions = array_merge($this->extensions, $extensions);

        return $this;
    }

    public function addType(DataTableTypeInterface $type): static
    {
        $this->types[] = $type;

        return $this;
    }

    public function addTypes(array $types): static
    {
        foreach ($types as $type) {
            $this->types[] = $type;
        }

        return $this;
    }

    public function addTypeExtension(DataTableTypeExtensionInterface $typeExtension): static
    {
        foreach ($typeExtension::getExtendedTypes() as $extendedType) {
            $this->typeExtensions[$extendedType][] = $typeExtension;
        }

        return $this;
    }

    public function addTypeExtensions(array $typeExtensions): static
    {
        foreach ($typeExtensions as $typeExtension) {
            $this->addTypeExtension($typeExtension);
        }

        return $this;
    }

    public function setProxyQueryFactory(?ProxyQueryFactoryInterface $proxyQueryFactory): static
    {
        $this->proxyQueryFactory = $proxyQueryFactory;

        return $this;
    }

    public function getDataTableFactory(): DataTableFactoryInterface
    {
        $extensions = $this->extensions;

        if (\count($this->types) > 0 || \count($this->typeExtensions) > 0) {
            $extensions[] = new PreloadedDataTableExtension($this->types, $this->typeExtensions);
        }

        $registry = new DataTableRegistry($extensions, $this->resolvedTypeFactory ?? new ResolvedDataTableTypeFactory());

        return new DataTableFactory($registry, $this->proxyQueryFactory);
    }
}
