<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\Exception\BadMethodCallException;
use Kreyu\Bundle\DataTableBundle\Filter\Form\Type\OperatorType;
use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeInterface;
use Kreyu\Bundle\DataTableBundle\LockableBuilderTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\ImmutableEventDispatcher;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormTypeInterface;

class FilterConfigBuilder implements FilterConfigBuilderInterface
{
    use LockableBuilderTrait;

    private array $attributes = [];
    private ?FilterHandlerInterface $handler = null;

    /**
     * @var class-string<FormTypeInterface>
     */
    private string $formType = TextType::class;

    /**
     * @var array<string, mixed>
     */
    private array $formOptions = [];

    /**
     * @var class-string<FormTypeInterface>
     */
    private string $operatorFormType = OperatorType::class;

    /**
     * @var array<string, mixed>
     */
    private array $operatorFormOptions = [];

    /**
     * @var array<Operator>
     */
    private array $supportedOperators = [];
    private Operator $defaultOperator = Operator::Equals;
    private bool $operatorSelectable = false;
    private FilterData $emptyData;

    public function __construct(
        private readonly string $name,
        private ResolvedFilterTypeInterface $type,
        private EventDispatcherInterface $dispatcher,
        private readonly array $options = [],
    ) {}

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

    public function getHandler(): FilterHandlerInterface
    {
        if ($this->handler === null) {
            throw new BadMethodCallException('Filter has no handler set');
        }

        return $this->handler;
    }

    public function setHandler(FilterHandlerInterface $handler): static
    {
        $this->ensureNotLocked();

        $this->handler = $handler;

        return $this;
    }

    public function getFormType(): string
    {
        return $this->formType;
    }

    public function setFormType(string $formType): static
    {
        $this->ensureNotLocked();

        $this->formType = $formType;

        return $this;
    }

    public function getFormOptions(): array
    {
        return $this->formOptions;
    }

    /**
     * @param array<string, mixed> $formOptions
     */
    public function setFormOptions(array $formOptions): static
    {
        $this->ensureNotLocked();

        $this->formOptions = $formOptions;

        return $this;
    }

    public function getOperatorFormType(): string
    {
        return $this->operatorFormType;
    }

    public function setOperatorFormType(string $operatorFormType): static
    {
        $this->ensureNotLocked();

        $this->operatorFormType = $operatorFormType;

        return $this;
    }

    public function getOperatorFormOptions(): array
    {
        return $this->operatorFormOptions;
    }

    /**
     * @param array<string, mixed> $operatorFormOptions
     */
    public function setOperatorFormOptions(array $operatorFormOptions): static
    {
        $this->ensureNotLocked();

        $this->operatorFormOptions = $operatorFormOptions;

        return $this;
    }

    public function getSupportedOperators(): array
    {
        return array_unique([...$this->supportedOperators, $this->defaultOperator], SORT_REGULAR);
    }

    public function setSupportedOperators(array $supportedOperators): static
    {
        $this->ensureNotLocked();

        $this->supportedOperators = $supportedOperators;

        return $this;
    }

    public function getDefaultOperator(): Operator
    {
        return $this->defaultOperator;
    }

    public function setDefaultOperator(Operator $defaultOperator): static
    {
        $this->ensureNotLocked();

        $this->defaultOperator = $defaultOperator;

        return $this;
    }

    public function isOperatorSelectable(): bool
    {
        return $this->operatorSelectable;
    }

    public function setOperatorSelectable(bool $operatorSelectable): static
    {
        $this->ensureNotLocked();

        $this->operatorSelectable = $operatorSelectable;

        return $this;
    }

    public function getEmptyData(): FilterData
    {
        return $this->emptyData ??= new FilterData();
    }

    public function setEmptyData(FilterData $emptyData): static
    {
        $this->ensureNotLocked();

        $this->emptyData = $emptyData;

        return $this;
    }

    public function getFilterConfig(): FilterConfigInterface
    {
        $this->ensureNotLocked();

        $config = clone $this;
        $config->locked = true;

        return $config;
    }
}
