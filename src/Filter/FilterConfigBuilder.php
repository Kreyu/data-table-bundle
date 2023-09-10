<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\Exception\BadMethodCallException;
use Kreyu\Bundle\DataTableBundle\Filter\Form\Type\OperatorType;
use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FilterConfigBuilder implements FilterConfigBuilderInterface
{
    protected bool $locked = false;

    private string $formType = TextType::class;
    private array $formOptions = [];
    private string $operatorFormType = OperatorType::class;
    private array $operatorFormOptions = [];
    private array $supportedOperators = [];
    private Operator $defaultOperator = Operator::Equals;
    private bool $operatorSelectable = false;

    public function __construct(
        private string $name,
        private ResolvedFilterTypeInterface $type,
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

    public function getType(): ResolvedFilterTypeInterface
    {
        return $this->type;
    }

    public function setType(ResolvedFilterTypeInterface $type): static
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

    public function getFormType(): string
    {
        return $this->formType;
    }

    public function setFormType(string $formType): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->formType = $formType;

        return $this;
    }

    public function getFormOptions(): array
    {
        return $this->formOptions;
    }

    public function setFormOptions(array $formOptions): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->formOptions = $formOptions;

        return $this;
    }

    public function getOperatorFormType(): string
    {
        return $this->operatorFormType;
    }

    public function setOperatorFormType(string $operatorFormType): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->operatorFormType = $operatorFormType;

        return $this;
    }

    public function getOperatorFormOptions(): array
    {
        return $this->operatorFormOptions;
    }

    public function setOperatorFormOptions(array $operatorFormOptions): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->operatorFormOptions = $operatorFormOptions;

        return $this;
    }

    public function getSupportedOperators(): array
    {
        return array_unique([...$this->supportedOperators, $this->defaultOperator], SORT_REGULAR);
    }

    public function setSupportedOperators(array $supportedOperators): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->supportedOperators = $supportedOperators;

        return $this;
    }

    public function getDefaultOperator(): Operator
    {
        // TODO: Remove "getNonDeprecatedCase()" call once the deprecated operators are removed.
        return $this->defaultOperator->getNonDeprecatedCase();
    }

    public function setDefaultOperator(Operator $defaultOperator): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->defaultOperator = $defaultOperator;

        return $this;
    }

    public function isOperatorSelectable(): bool
    {
        return $this->operatorSelectable;
    }

    public function setOperatorSelectable(bool $operatorSelectable): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->operatorSelectable = $operatorSelectable;

        return $this;
    }

    public function getFilterConfig(): FilterConfigInterface
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
        return new BadMethodCallException('FilterConfigBuilder methods cannot be accessed anymore once the builder is turned into a FilterConfigInterface instance.');
    }
}
