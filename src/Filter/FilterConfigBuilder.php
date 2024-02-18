<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\Exception\BadMethodCallException;
use Kreyu\Bundle\DataTableBundle\Filter\Form\Type\OperatorType;
use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\ImmutableEventDispatcher;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FilterConfigBuilder implements FilterConfigBuilderInterface
{
    protected bool $locked = false;

    private FilterHandlerInterface $handler;
    private string $formType = TextType::class;
    private array $formOptions = [];
    private string $operatorFormType = OperatorType::class;
    private array $operatorFormOptions = [];
    private array $supportedOperators = [];
    private Operator $defaultOperator = Operator::Equals;
    private bool $operatorSelectable = false;
    private FilterData $emptyData;

    public function __construct(
        private readonly string $name,
        private ResolvedFilterTypeInterface $type,
        private EventDispatcherInterface $dispatcher,
        private readonly array $options = [],
    ) {
    }

    public function addEventListener(string $eventName, callable $listener, int $priority = 0): static
    {
        $this->dispatcher->addListener($eventName, $listener, $priority);

        return $this;
    }

    public function addEventSubscriber(EventSubscriberInterface $subscriber): static
    {
        $this->dispatcher->addSubscriber($subscriber);

        return $this;
    }

    public function getEventDispatcher(): EventDispatcherInterface
    {
        if (!$this->dispatcher instanceof ImmutableEventDispatcher) {
            $this->dispatcher = new ImmutableEventDispatcher($this->dispatcher);
        }

        return $this->dispatcher;
    }

    public function getName(): string
    {
        return $this->name;
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
        return array_key_exists($name, $this->options) ? $this->options[$name] : $default;
    }

    public function getHandler(): FilterHandlerInterface
    {
        if (!isset($this->handler)) {
            throw new BadMethodCallException('Filter has no handler set');
        }

        return $this->handler;
    }

    public function setHandler(FilterHandlerInterface $handler): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->handler = $handler;

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
        return $this->defaultOperator;
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

    public function getEmptyData(): FilterData
    {
        return $this->emptyData ??= new FilterData();
    }

    public function setEmptyData(FilterData $emptyData): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->emptyData = $emptyData;

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
