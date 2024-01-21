<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Exception\ExceptionInterface;
use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;

/**
 * @template TType
 * @template TResolvedType
 * @template TExtension
 *
 * @internal
 */
abstract class AbstractRegistry
{
    /**
     * @var array<string, TResolvedType>
     */
    private array $types = [];

    /**
     * @var array<class-string<TType>, bool>
     */
    private array $checkedTypes = [];

    /**
     * @param iterable<TExtension> $extensions
     */
    public function __construct(
        private readonly iterable $extensions,
        private readonly mixed $resolvedTypeFactory,
    ) {
        $extensionClass = $this->getExtensionClass();

        foreach ($extensions as $extension) {
            if (!$extension instanceof $extensionClass) {
                throw new UnexpectedTypeException($extension, $extensionClass);
            }
        }
    }

    public function hasType(string $name): bool
    {
        if (isset($this->types[$name])) {
            return true;
        }

        try {
            $this->doGetType($name);
        } catch (ExceptionInterface) {
            return false;
        }

        return true;
    }

    /**
     * @return iterable<TExtension>
     */
    public function getExtensions(): iterable
    {
        return $this->extensions;
    }

    /**
     * @return TResolvedType
     */
    protected function doGetType(string $name)
    {
        if (!isset($this->types[$name])) {
            $type = null;

            foreach ($this->extensions as $extension) {
                if ($extension->hasType($name)) {
                    $type = $extension->getType($name);
                    break;
                }
            }

            if (!$type) {
                $typeClass = $this->getTypeClass();

                if (!class_exists($name)) {
                    throw new InvalidArgumentException(sprintf('Could not load %s type "%s": class does not exist.', $this->getErrorContextName(), $name));
                }

                if (!is_subclass_of($name, $typeClass)) {
                    throw new InvalidArgumentException(sprintf('Could not load %s type "%s": class does not implement "%s".', $this->getErrorContextName(), $name, $typeClass));
                }

                $type = new $name();
            }

            $this->types[$name] = $this->resolveType($type);
        }

        return $this->types[$name];
    }

    /**
     * @return TResolvedType
     */
    private function resolveType($type)
    {
        $parentType = $type->getParent();
        $fqcn = $type::class;

        if (isset($this->checkedTypes[$fqcn])) {
            $types = implode(' > ', array_merge(array_keys($this->checkedTypes), [$fqcn]));
            throw new \LogicException(sprintf('Circular reference detected for %s type "%s" (%s).', $this->getErrorContextName(), $fqcn, $types));
        }

        $this->checkedTypes[$fqcn] = true;

        $typeExtensions = [];

        try {
            foreach ($this->extensions as $extension) {
                $typeExtensions[] = $extension->getTypeExtensions($fqcn);
            }

            return $this->resolvedTypeFactory->createResolvedType(
                $type,
                array_merge([], ...$typeExtensions),
                $parentType ? $this->getType($parentType) : null,
            );
        } finally {
            unset($this->checkedTypes[$fqcn]);
        }
    }

    /**
     * @return TResolvedType
     */
    abstract public function getType(string $name);

    /**
     * @return class-string<TType>
     */
    abstract protected function getTypeClass(): string;

    /**
     * @return class-string<TExtension>
     */
    abstract protected function getExtensionClass(): string;

    abstract protected function getErrorContextName(): string;
}
