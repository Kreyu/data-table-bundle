<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormTypeInterface;

interface FilterConfigInterface
{
    public function getEventDispatcher(): EventDispatcherInterface;

    public function getName(): string;

    public function getType(): ResolvedFilterTypeInterface;

    public function getOptions(): array;

    public function hasOption(string $name): bool;

    public function getOption(string $name, mixed $default = null): mixed;

    public function getAttributes(): array;

    public function hasAttribute(string $name): bool;

    public function getAttribute(string $name, mixed $default = null): mixed;

    public function getHandler(): FilterHandlerInterface;

    /**
     * @return class-string<FormTypeInterface>
     */
    public function getFormType(): string;

    public function getFormOptions(): array;

    /**
     * @return class-string<FormTypeInterface>
     */
    public function getOperatorFormType(): string;

    public function getOperatorFormOptions(): array;

    /**
     * @return array<Operator>
     */
    public function getSupportedOperators(): array;

    public function getDefaultOperator(): Operator;

    public function isOperatorSelectable(): bool;
}
