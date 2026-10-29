<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter;

use Kreyu\Bundle\DataTableBundle\Exporter\Type\ResolvedExporterTypeInterface;
use Kreyu\Bundle\DataTableBundle\LockableBuilderTrait;

class ExporterConfigBuilder implements ExporterConfigBuilderInterface
{
    use LockableBuilderTrait;

    private array $attributes = [];

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        private string $name,
        private ResolvedExporterTypeInterface $type,
        private array $options = [],
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

    public function getType(): ResolvedExporterTypeInterface
    {
        return $this->type;
    }

    public function setType(ResolvedExporterTypeInterface $type): static
    {
        $this->ensureNotLocked();

        $this->type = $type;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
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

    /**
     * @param array<string, mixed> $options
     */
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

    public function setAttribute(string $name, mixed $value): static
    {
        $this->ensureNotLocked();

        $this->attributes[$name] = $value;

        return $this;
    }

    public function setAttributes(array $attributes): static
    {
        $this->ensureNotLocked();

        $this->attributes = $attributes;

        return $this;
    }

    public function getExporterConfig(): ExporterConfigInterface
    {
        $this->ensureNotLocked();

        $config = clone $this;
        $config->locked = true;

        return $config;
    }
}
