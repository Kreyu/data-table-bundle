<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action;

use Kreyu\Bundle\DataTableBundle\Action\Extension\ActionExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Action\Extension\ActionTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Action\Extension\PreloadedActionExtension;
use Kreyu\Bundle\DataTableBundle\Action\Type\ActionTypeInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\ResolvedActionTypeFactory;
use Kreyu\Bundle\DataTableBundle\Action\Type\ResolvedActionTypeFactoryInterface;

class ActionFactoryBuilder implements ActionFactoryBuilderInterface
{
    private ResolvedActionTypeFactoryInterface $resolvedTypeFactory;
    private array $extensions = [];
    private array $types = [];
    private array $typeExtensions = [];

    public function setResolvedTypeFactory(ResolvedActionTypeFactoryInterface $resolvedTypeFactory): static
    {
        $this->resolvedTypeFactory = $resolvedTypeFactory;

        return $this;
    }

    public function addExtension(ActionExtensionInterface $extension): static
    {
        $this->extensions[] = $extension;

        return $this;
    }

    public function addExtensions(array $extensions): static
    {
        $this->extensions = array_merge($this->extensions, $extensions);

        return $this;
    }

    public function addType(ActionTypeInterface $type): static
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

    public function addTypeExtension(ActionTypeExtensionInterface $typeExtension): static
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

    public function getActionFactory(): ActionFactoryInterface
    {
        $extensions = $this->extensions;

        if (\count($this->types) > 0 || \count($this->typeExtensions) > 0) {
            $extensions[] = new PreloadedActionExtension($this->types, $this->typeExtensions);
        }

        $registry = new ActionRegistry($extensions, $this->resolvedTypeFactory ?? new ResolvedActionTypeFactory());

        return new ActionFactory($registry);
    }
}
