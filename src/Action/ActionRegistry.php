<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action;

use Kreyu\Bundle\DataTableBundle\AbstractRegistry;
use Kreyu\Bundle\DataTableBundle\Action\Extension\ActionExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\ActionTypeInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\ResolvedActionTypeInterface;

/**
 * @extends AbstractRegistry<ActionTypeInterface, ResolvedActionTypeInterface, ActionExtensionInterface>
 */
class ActionRegistry extends AbstractRegistry implements ActionRegistryInterface
{
    public function getType(string $name): ResolvedActionTypeInterface
    {
        return $this->doGetType($name);
    }

    final protected function getErrorContextName(): string
    {
        return 'action';
    }

    final protected function getTypeClass(): string
    {
        return ActionTypeInterface::class;
    }

    final protected function getExtensionClass(): string
    {
        return ActionExtensionInterface::class;
    }
}
