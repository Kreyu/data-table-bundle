<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action\Extension\DependencyInjection;

use Kreyu\Bundle\DataTableBundle\AbstractDependencyInjectionExtension;
use Kreyu\Bundle\DataTableBundle\Action\Extension\ActionExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\ActionTypeInterface;

class DependencyInjectionActionExtension extends AbstractDependencyInjectionExtension implements ActionExtensionInterface
{
    public function getType(string $name): ActionTypeInterface
    {
        return $this->doGetType($name);
    }

    protected function getTypeClass(): string
    {
        return ActionTypeInterface::class;
    }

    protected function getErrorContextName(): string
    {
        return 'filter';
    }
}
