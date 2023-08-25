<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action\Type;

use Kreyu\Bundle\DataTableBundle\Action\Extension\ActionTypeExtensionInterface;

interface ResolvedActionTypeFactoryInterface
{
    /**
     * @param array<ActionTypeExtensionInterface> $typeExtensions
     */
    public function createResolvedType(ActionTypeInterface $type, array $typeExtensions = [], ResolvedActionTypeInterface $parent = null): ResolvedActionTypeInterface;
}
