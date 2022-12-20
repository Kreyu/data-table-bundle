<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\Form\Type\OperatorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;

abstract class AbstractFilter implements FilterInterface
{
    protected string $name;
    protected array $options = [];

    public function initialize(string $name, array $options = []): void
    {
        $this->name = $name;

        $this->configureOptions($optionsResolver = new OptionsResolver());

        $this->options = $optionsResolver->resolve($options);
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'label' => $this->getName(),
                'field_name' => $this->getName(),
                'field_type' => TextType::class,
                'field_options' => [],
                'operator_type' => OperatorType::class,
                'operator_options' => [],
            ])
            ->setRequired([
                'label',
                'field_name',
                'field_type',
                'field_options',
                'operator_type',
                'operator_options',
            ])
            ->setAllowedTypes('label', ['string', TranslatableMessage::class])
            ->setAllowedTypes('field_name', ['string'])
            ->setAllowedTypes('field_type', ['string'])
            ->setAllowedTypes('field_options', ['array'])
            ->setAllowedTypes('operator_type', ['string'])
            ->setAllowedTypes('operator_options', ['array'])
        ;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFormName(): string
    {
        return str_replace('.', '__', $this->getName());
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getOption(string $key, mixed $default = null): mixed
    {
        return $this->options[$key] ?? $default;
    }

    public function getLabel(): string
    {
        return (string) $this->getOption('label');
    }

    public function getFieldName(): string
    {
        return $this->getOption('field_name');
    }

    public function getFieldType(): string
    {
        return $this->getOption('field_type');
    }

    public function getFieldOptions(): array
    {
        return $this->getOption('field_options');
    }

    public function getOperatorType(): string
    {
        return $this->getOption('operator_type');
    }

    public function getOperatorOptions(): array
    {
        return $this->getOption('operator_options') + [
            'choices' => $this->getSupportedOperators(),
        ];
    }

    public function getFormOptions(): array
    {
        return [
            'field_type' => $this->getFieldType(),
            'field_options' => $this->getFieldOptions(),
            'operator_type' => $this->getOperatorType(),
            'operator_options' => $this->getOperatorOptions(),
            'label' => $this->getLabel(),
        ];
    }

    /**
     * @return array<Operator>
     */
    protected abstract function getSupportedOperators(): array;
}
