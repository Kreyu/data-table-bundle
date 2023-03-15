<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action\Type;

interface ResolvedActionTypeFactoryInterface
{
    public function createResolvedType(ActionTypeInterface $type, array $typeExtensions, ResolvedActionTypeInterface $parent = null): ResolvedActionTypeInterface;
}
