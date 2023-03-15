<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action\Type;

class ResolvedActionTypeFactory implements ResolvedActionTypeFactoryInterface
{
    public function createResolvedType(ActionTypeInterface $type, array $typeExtensions, ResolvedActionTypeInterface $parent = null): ResolvedActionTypeInterface
    {
        return new ResolvedActionType($type, $typeExtensions, $parent);
    }
}
