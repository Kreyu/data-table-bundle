<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormTypeInterface;

interface FilterConfigBuilderInterface extends FilterConfigInterface
{
    public function addEventListener(string $eventName, callable $listener, int $priority = 0): static;

    public function addEventSubscriber(EventSubscriberInterface $subscriber): static;

    public function setType(ResolvedFilterTypeInterface $type): static;

    public function setHandler(FilterHandlerInterface $handler): static;

    public function setAttribute(string $name, mixed $value): static;

    public function setAttributes(array $attributes): static;

    /**
     * @param class-string<FormTypeInterface> $formType
     */
    public function setFormType(string $formType): static;

    public function setFormOptions(array $formOptions): static;

    /**
     * @param class-string<FormTypeInterface> $operatorFormType
     */
    public function setOperatorFormType(string $operatorFormType): static;

    public function setOperatorFormOptions(array $operatorFormOptions): static;

    /**
     * @param array<Operator> $supportedOperators
     */
    public function setSupportedOperators(array $supportedOperators): static;

    public function setDefaultOperator(Operator $defaultOperator): static;

    public function setOperatorSelectable(bool $operatorSelectable): static;

    public function getFilterConfig(): FilterConfigInterface;

    public function setIsHeaderFilter(bool $isHeaderFilter): self;
}
