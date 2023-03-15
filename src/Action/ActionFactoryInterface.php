<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action;

use Kreyu\Bundle\DataTableBundle\Action\Type\ActionTypeInterface;

interface ActionFactoryInterface
{
    /**
     * @param class-string<ActionTypeInterface> $type
     */
    public function create(string $name, string $type, array $options = []): ActionInterface;
}
