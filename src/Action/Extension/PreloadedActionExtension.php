<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action\Extension;

use Kreyu\Bundle\DataTableBundle\Action\Type\ActionTypeInterface;

class PreloadedActionExtension extends AbstractActionExtension
{
    /**
     * @param array<ActionTypeInterface>                         $types
     * @param array<string, array<ActionTypeExtensionInterface>> $typeExtensions
     */
    public function __construct(
        private readonly array $types = [],
        private readonly array $typeExtensions = [],
    ) {
    }

    protected function loadTypes(): array
    {
        return $this->types;
    }

    protected function loadTypeExtensions(): array
    {
        return $this->typeExtensions;
    }
}
