<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Extension;

use Kreyu\Bundle\DataTableBundle\DataTableExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Type\DataTableTypeInterface;

class PreloadedDataTableExtension implements DataTableExtensionInterface
{
    private readonly array $types;

    public function __construct(
        array $types = [],
        private readonly array $typeExtensions = [],
    ) {
        foreach ($types as $type) {
            $this->types[$type::class] = $type;
        }
    }

    public function getType(string $name): DataTableTypeInterface
    {
        if (!isset($this->types[$name])) {
            throw new InvalidArgumentException(sprintf('The type "%s" cannot be loaded by this extension.', $name));
        }

        return $this->types[$name];
    }

    public function hasType(string $name): bool
    {
        return isset($this->types[$name]);
    }

    public function getTypeExtensions(string $name): array
    {
        return $this->typeExtensions[$name] ?? [];
    }

    public function hasTypeExtensions(string $name): bool
    {
        return !empty($this->typeExtensions[$name]);
    }
}
