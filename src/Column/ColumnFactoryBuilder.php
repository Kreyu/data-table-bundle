<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\Column\Extension\ColumnExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Column\Extension\ColumnTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Column\Extension\CoreColumnExtension;
use Kreyu\Bundle\DataTableBundle\Column\Extension\PreloadedColumnExtension;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeFactory;
use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeFactoryInterface;

class ColumnFactoryBuilder implements ColumnFactoryBuilderInterface
{
    private ResolvedColumnTypeFactoryInterface $resolvedTypeFactory;

    /**
     * @var array<ColumnExtensionInterface>
     */
    private array $extensions = [];

    /**
     * @var array<ColumnTypeInterface>
     */
    private array $types = [];

    /**
     * @var array<string, array<ColumnTypeExtensionInterface>>
     */
    private array $typeExtensions = [];

    public function __construct(
        private readonly bool $forceCoreExtension = false,
    ) {
    }

    public function setResolvedTypeFactory(ResolvedColumnTypeFactoryInterface $resolvedTypeFactory): static
    {
        $this->resolvedTypeFactory = $resolvedTypeFactory;

        return $this;
    }

    public function addExtension(ColumnExtensionInterface $extension): static
    {
        $this->extensions[] = $extension;

        return $this;
    }

    public function addExtensions(array $extensions): static
    {
        $this->extensions = array_merge($this->extensions, $extensions);

        return $this;
    }

    public function addType(ColumnTypeInterface $type): static
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

    public function addTypeExtension(ColumnTypeExtensionInterface $typeExtension): static
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

    public function getColumnFactory(): ColumnFactoryInterface
    {
        $extensions = $this->extensions;

        if ($this->forceCoreExtension) {
            $hasCoreExtension = false;

            foreach ($extensions as $extension) {
                if ($extension instanceof CoreColumnExtension) {
                    $hasCoreExtension = true;
                    break;
                }
            }

            if (!$hasCoreExtension) {
                array_unshift($extensions, new CoreColumnExtension());
            }
        }

        if (\count($this->types) > 0 || \count($this->typeExtensions) > 0) {
            $extensions[] = new PreloadedColumnExtension($this->types, $this->typeExtensions);
        }

        $registry = new ColumnRegistry($extensions, $this->resolvedTypeFactory ?? new ResolvedColumnTypeFactory());

        return new ColumnFactory($registry);
    }
}
