<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterTypeInterface;

class FilterFactory implements FilterFactoryInterface
{
    public function __construct(
        private FilterRegistryInterface $registry,
    ) {
    }

    /**
     * @param class-string<FilterTypeInterface> $type
     */
    public function create(string $name, string $type, array $options = []): FilterInterface
    {
        $type = $this->registry->getType($type);

        $optionsResolver = $type->getOptionsResolver();

        return new Filter($name, $type, $optionsResolver->resolve($options));
    }
}
