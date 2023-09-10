<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Exception\BadMethodCallException;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

class ColumnConfigBuilder implements ColumnConfigBuilderInterface
{
    protected bool $locked = false;

    private array $attributes = [];
    private ?PropertyPathInterface $propertyPath = null;
    private ?PropertyPathInterface $sortPropertyPath = null;
    private bool $sortable = false;
    private bool $exportable = false;
    private bool $personalizable = true;
    private ColumnFactoryInterface $columnFactory;

    public function __construct(
        private /* readonly */ string $name,
        private ResolvedColumnTypeInterface $type,
        private /* readonly */ array $options = [],
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->name = $name;

        return $this;
    }

    public function getType(): ResolvedColumnTypeInterface
    {
        return $this->type;
    }

    public function setType(ResolvedColumnTypeInterface $type): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->type = $type;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function hasOption(string $name): bool
    {
        return array_key_exists($name, $this->options);
    }

    public function getOption(string $name, mixed $default = null): mixed
    {
        return $this->options[$name] ?? $default;
    }

    public function setOptions(array $options): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->options = $options;

        return $this;
    }

    public function setOption(string $name, mixed $value): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->options[$name] = $value;

        return $this;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function hasAttribute(string $name): bool
    {
        return array_key_exists($name, $this->attributes);
    }

    public function getAttribute(string $name, mixed $default = null): mixed
    {
        return $this->attributes[$name] ?? $default;
    }

    public function setAttributes(array $attributes): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->attributes = $attributes;

        return $this;
    }

    public function setAttribute(string $name, mixed $value): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->attributes[$name] = $value;

        return $this;
    }

    public function getPropertyPath(): ?PropertyPathInterface
    {
        return $this->propertyPath;
    }

    public function setPropertyPath(null|string|PropertyPathInterface $propertyPath): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        if (is_string($propertyPath)) {
            $propertyPath = new PropertyPath($propertyPath);
        }

        $this->propertyPath = $propertyPath;

        return $this;
    }

    public function getSortPropertyPath(): ?PropertyPathInterface
    {
        return $this->sortPropertyPath;
    }

    public function setSortPropertyPath(null|string|PropertyPathInterface $sortPropertyPath): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        if (is_string($sortPropertyPath)) {
            $sortPropertyPath = new PropertyPath($sortPropertyPath);
        }

        $this->sortPropertyPath = $sortPropertyPath;

        return $this;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function setSortable(bool $sortable): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->sortable = $sortable;

        return $this;
    }

    public function isExportable(): bool
    {
        return $this->exportable;
    }

    public function setExportable(bool $exportable): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->exportable = $exportable;

        return $this;
    }

    public function isPersonalizable(): bool
    {
        return $this->personalizable;
    }

    public function setPersonalizable(bool $personalizable): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->personalizable = $personalizable;

        return $this;
    }

    public function getColumnFactory(): ColumnFactoryInterface
    {
        if (!isset($this->columnFactory)) {
            throw new BadMethodCallException('The column factory must be set before retrieving it.');
        }

        return $this->columnFactory;
    }

    public function setColumnFactory(ColumnFactoryInterface $columnFactory): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->columnFactory = $columnFactory;

        return $this;
    }

    public function getColumnConfig(): ColumnConfigInterface
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $config = clone $this;
        $config->locked = true;

        return $config;
    }

    private function createBuilderLockedException(): BadMethodCallException
    {
        return new BadMethodCallException('ColumnConfigBuilder methods cannot be accessed anymore once the builder is turned into a ColumnConfigInterface instance.');
    }
}
