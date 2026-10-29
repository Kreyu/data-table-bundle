<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Exception\BadMethodCallException;
use Kreyu\Bundle\DataTableBundle\LockableBuilderTrait;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

class ColumnConfigBuilder implements ColumnConfigBuilderInterface
{
    use LockableBuilderTrait;

    private array $attributes = [];
    private ?PropertyPathInterface $propertyPath = null;
    private ?PropertyPathInterface $sortPropertyPath = null;
    private bool $sortable = false;
    private bool $exportable = false;
    private bool $personalizable = true;
    private int $priority = 0;
    private bool $visible = true;
    private ?ColumnFactoryInterface $columnFactory = null;

    public function __construct(
        /* readonly */ private string $name,
        private ResolvedColumnTypeInterface $type,
        /* readonly */ private array $options = [],
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->ensureNotLocked();

        $this->name = $name;

        return $this;
    }

    public function getType(): ResolvedColumnTypeInterface
    {
        return $this->type;
    }

    public function setType(ResolvedColumnTypeInterface $type): static
    {
        $this->ensureNotLocked();

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
        return array_key_exists($name, $this->options) ? $this->options[$name] : $default;
    }

    public function setOptions(array $options): static
    {
        $this->ensureNotLocked();

        $this->options = $options;

        return $this;
    }

    public function setOption(string $name, mixed $value): static
    {
        $this->ensureNotLocked();

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
        return array_key_exists($name, $this->attributes) ? $this->attributes[$name] : $default;
    }

    public function setAttributes(array $attributes): static
    {
        $this->ensureNotLocked();

        $this->attributes = $attributes;

        return $this;
    }

    public function setAttribute(string $name, mixed $value): static
    {
        $this->ensureNotLocked();

        $this->attributes[$name] = $value;

        return $this;
    }

    public function getPropertyPath(): ?PropertyPathInterface
    {
        return $this->propertyPath;
    }

    public function setPropertyPath(string|PropertyPathInterface|null $propertyPath): static
    {
        $this->ensureNotLocked();

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

    public function setSortPropertyPath(string|PropertyPathInterface|null $sortPropertyPath): static
    {
        $this->ensureNotLocked();

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
        $this->ensureNotLocked();

        $this->sortable = $sortable;

        return $this;
    }

    public function isExportable(): bool
    {
        return $this->exportable;
    }

    public function setExportable(bool $exportable): static
    {
        $this->ensureNotLocked();

        $this->exportable = $exportable;

        return $this;
    }

    public function isPersonalizable(): bool
    {
        return $this->personalizable;
    }

    public function setPersonalizable(bool $personalizable): static
    {
        $this->ensureNotLocked();

        $this->personalizable = $personalizable;

        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): static
    {
        $this->ensureNotLocked();

        $this->priority = $priority;

        return $this;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): static
    {
        $this->ensureNotLocked();

        $this->visible = $visible;

        return $this;
    }

    public function getColumnFactory(): ColumnFactoryInterface
    {
        if ($this->columnFactory === null) {
            throw new BadMethodCallException('The column factory must be set before retrieving it.');
        }

        return $this->columnFactory;
    }

    public function setColumnFactory(ColumnFactoryInterface $columnFactory): static
    {
        $this->ensureNotLocked();

        $this->columnFactory = $columnFactory;

        return $this;
    }

    public function getColumnConfig(): ColumnConfigInterface
    {
        $this->ensureNotLocked();

        $config = clone $this;
        $config->locked = true;

        return $config;
    }
}
