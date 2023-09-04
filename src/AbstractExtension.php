<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;

/**
 * @template TType
 * @template TTypeExtension
 *
 * @internal
 */
abstract class AbstractExtension
{
    /**
     * @var array<TType>
     */
    private array $types = [];

    /**
     * @var array<string, array<TTypeExtension>>
     */
    private array $typeExtensions = [];

    public function hasType(string $name): bool
    {
        if (!isset($this->types)) {
            $this->initTypes();
        }

        return isset($this->types[$name]);
    }

    public function getTypeExtensions(string $name): array
    {
        if (!isset($this->typeExtensions)) {
            $this->initTypeExtensions();
        }

        return $this->typeExtensions[$name] ?? [];
    }

    public function hasTypeExtensions(string $name): bool
    {
        if (!isset($this->typeExtensions)) {
            $this->initTypeExtensions();
        }

        return isset($this->typeExtensions[$name]) && \count($this->typeExtensions[$name]) > 0;
    }


    /**
     * @return array<TType>
     */
    protected function loadTypes(): array
    {
        return [];
    }

    /**
     * @return array<TTypeExtension>
     */
    protected function loadTypeExtensions(): array
    {
        return [];
    }

    /**
     * @return class-string<TType>
     */
    abstract protected function getTypeClass(): string;

    /**
     * @return class-string<TTypeExtension>
     */
    abstract protected function getTypeExtensionClass(): string;

    abstract protected function getErrorContextName(): string;

    protected function doGetType(string $name)
    {
        if (!isset($this->types)) {
            $this->initTypes();
        }

        if (!isset($this->types[$name])) {
            throw new InvalidArgumentException(sprintf('The %s type "%s" cannot be loaded by this extension.', $this->getErrorContextName(), $name));
        }

        return $this->types[$name];
    }

    private function initTypes(): void
    {
        $this->types = [];

        $typeClass = $this->getTypeClass();

        foreach ($this->loadTypes() as $type) {
            if (!$type instanceof $typeClass) {
                throw new UnexpectedTypeException($type, $typeClass);
            }

            $this->types[$type::class] = $type;
        }
    }

    private function initTypeExtensions(): void
    {
        $this->typeExtensions = [];

        $typeExtensionClass = $this->getTypeExtensionClass();

        foreach ($this->loadTypeExtensions() as $extension) {
            if (!$extension instanceof $typeExtensionClass) {
                throw new UnexpectedTypeException($extension, $typeExtensionClass);
            }

            foreach ($extension::getExtendedTypes() as $extendedType) {
                $this->typeExtensions[$extendedType][] = $extension;
            }
        }
    }
}