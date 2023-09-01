<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeInterface;
use Symfony\Component\Form\FormTypeInterface;

interface FilterConfigBuilderInterface extends FilterConfigInterface
{
    /**
     * @deprecated provide the name using the factory {@see FilterFactoryInterface} "named" methods instead
     */
    public function setName(string $name): static;

    public function setType(ResolvedFilterTypeInterface $type): static;

    /**
     * @deprecated modifying the options dynamically will be removed as it creates unexpected behaviors
     */
    public function setOptions(array $options): static;

    /**
     * @deprecated modifying the options dynamically will be removed as it creates unexpected behaviors
     */
    public function setOption(string $name, mixed $value): static;

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
}
