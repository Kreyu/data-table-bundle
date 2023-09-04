<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action;

use Kreyu\Bundle\DataTableBundle\Action\Extension\ActionExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Action\Extension\ActionTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\ActionTypeInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\ResolvedActionTypeFactoryInterface;

interface ActionFactoryBuilderInterface
{
    public function setResolvedTypeFactory(ResolvedActionTypeFactoryInterface $resolvedTypeFactory): static;

    public function addExtension(ActionExtensionInterface $extension): static;

    /**
     * @param array<ActionExtensionInterface> $extensions
     */
    public function addExtensions(array $extensions): static;

    public function addType(ActionTypeInterface $type): static;

    /**
     * @param array<ActionTypeInterface> $types
     */
    public function addTypes(array $types): static;

    public function addTypeExtension(ActionTypeExtensionInterface $typeExtension): static;

    /**
     * @param array<ActionTypeExtensionInterface> $typeExtensions
     */
    public function addTypeExtensions(array $typeExtensions): static;

    public function getActionFactory(): ActionFactoryInterface;
}