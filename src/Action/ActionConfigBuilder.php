<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action;

use Kreyu\Bundle\DataTableBundle\Action\Type\ResolvedActionTypeInterface;
use Kreyu\Bundle\DataTableBundle\Exception\BadMethodCallException;

class ActionConfigBuilder implements ActionConfigBuilderInterface
{
    protected bool $locked = false;

    private ActionContext $context = ActionContext::Global;
    private array $attributes = [];
    private bool $confirmable = false;

    public function __construct(
        private string $name,
        private ResolvedActionTypeInterface $type,
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

    public function getType(): ResolvedActionTypeInterface
    {
        return $this->type;
    }

    public function setType(ResolvedActionTypeInterface $type): static
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

    public function getContext(): ActionContext
    {
        return $this->context;
    }

    public function setContext(ActionContext $context): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->context = $context;

        return $this;
    }

    public function isConfirmable(): bool
    {
        return $this->confirmable;
    }

    public function setConfirmable(bool $confirmable): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->confirmable = $confirmable;

        return $this;
    }

    public function getActionConfig(): ActionConfigInterface
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
        return new BadMethodCallException('ActionConfigBuilder methods cannot be accessed anymore once the builder is turned into a ActionConfigInterface instance.');
    }
}
