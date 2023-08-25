<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeInterface;
use Symfony\Component\Form\FormTypeInterface;

interface FilterConfigBuilderInterface extends FilterConfigInterface
{
    public function setName(string $name): static;

    public function setType(ResolvedFilterTypeInterface $type): static;

    public function setOptions(array $options): static;

    public function setOption(string $name, mixed $value): static;

    /**
     * @param class-string<FormTypeInterface> $valueFormType
     */
    public function setValueFormType(string $valueFormType): static;

    public function setValueFormOptions(array $valueFormOptions): static;

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
}
