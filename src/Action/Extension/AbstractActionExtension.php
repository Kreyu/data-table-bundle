<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action\Extension;

use Kreyu\Bundle\DataTableBundle\AbstractExtension;
use Kreyu\Bundle\DataTableBundle\Action\Type\ActionTypeInterface;

/**
 * @extends AbstractExtension<ActionTypeInterface, ActionTypeExtensionInterface>
 */
abstract class AbstractActionExtension extends AbstractExtension implements ActionExtensionInterface
{
    public function getType(string $name): ActionTypeInterface
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

    final protected function getTypeExtensionClass(): string
    {
        return ActionTypeExtensionInterface::class;
    }
}