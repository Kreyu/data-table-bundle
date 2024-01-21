<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action;

use Kreyu\Bundle\DataTableBundle\Action\Extension\ActionExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\ActionTypeInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\ResolvedActionTypeInterface;

interface ActionRegistryInterface
{
    /**
     * @param class-string<ActionTypeInterface> $name
     */
    public function getType(string $name): ResolvedActionTypeInterface;

    /**
     * @return iterable<ActionExtensionInterface>
     */
    public function getExtensions(): iterable;
}
