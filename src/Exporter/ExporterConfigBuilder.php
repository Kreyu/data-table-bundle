<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter;

use Kreyu\Bundle\DataTableBundle\Exception\BadMethodCallException;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ResolvedExporterTypeInterface;

class ExporterConfigBuilder implements ExporterConfigBuilderInterface
{
    protected bool $locked = false;

    public function __construct(
        private string $name,
        private ResolvedExporterTypeInterface $type,
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

    public function getType(): ResolvedExporterTypeInterface
    {
        return $this->type;
    }

    public function setType(ResolvedExporterTypeInterface $type): static
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

    public function getExporterConfig(): ExporterConfigInterface
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
        return new BadMethodCallException('ExporterConfigBuilder methods cannot be accessed anymore once the builder is turned into a ExporterConfigInterface instance.');
    }
}