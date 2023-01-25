<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;

class ColumnFactory implements ColumnFactoryInterface
{
    public function __construct(
        private ColumnRegistryInterface $registry,
    ) {
    }

    /**
     * @param class-string<ColumnTypeInterface> $type
     */
    public function create(string $name, string $type, array $options = []): ColumnInterface
    {
        $type = $this->registry->getType($type);

        $optionsResolver = $type->getOptionsResolver();

        return new Column($name, $type, $optionsResolver->resolve($options));
    }
}
