<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Exception\BadMethodCallException;

class ColumnBuilder implements ColumnBuilderInterface
{
    private array $attributes = [];
    private bool $sortable = false;
    private bool $exportable = false;
    private bool $locked = false;

    public function __construct(
        private string $name,
        private ResolvedColumnTypeInterface $type,
        private array $options = [],
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

    public function setAttribute(string $name, mixed $value = null): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->attributes[$name] = $value;

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

    public function getColumnConfig(): ColumnConfigInterface
    {
        $config = clone $this;
        $config->locked = true;

        return $config;
    }

    public function getColumn(): ColumnInterface
    {
        return new Column($this->getColumnConfig());
    }

    private function createBuilderLockedException(): BadMethodCallException
    {
        return new BadMethodCallException('ColumnConfigBuilder methods cannot be accessed anymore once the builder is turned into a ColumnConfigInterface instance.');
    }
}