<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Symfony\Component\Form\Extension\Core\Type\TextType;

abstract class AbstractFilter implements FilterInterface
{
    protected string $name;
    protected array $options = [];

    public function initialize(string $name, array $options = []): void
    {
        $this->name = $name;
        $this->options = $options;
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

    public function setOption(string $key, mixed $value): void
    {
        $this->options[$key] = $value;
    }

    public function getFieldName(): ?string
    {
        $fieldName = $this->getOption('field_name');

        if (null === $fieldName) {
            throw new \RuntimeException(sprintf(
                'The option `field_name` must be set for field: `%s`',
                $this->getName()
            ));
        }

        return $fieldName;
    }

    public function getFieldMapping(): array
    {
        $fieldMapping = $this->getOption('field_mapping');

        if (null === $fieldMapping) {
            throw new \RuntimeException(sprintf(
                'The option `field_mapping` must be set for field: `%s`',
                $this->getName()
            ));
        }

        return $fieldMapping;
    }

    public function getAssociationMapping(): array
    {
        $associationMapping = $this->getOption('association_mapping');

        if (null === $associationMapping) {
            throw new \RuntimeException(sprintf(
                'The option `association_mapping` must be set for field: `%s`',
                $this->getName()
            ));
        }

        return $associationMapping;
    }

    public function getFieldType(): string
    {
        return $this->getOption('field_type', TextType::class);
    }

    public function getFieldOptions(): array
    {
        return $this->getOption('field_options', []);
    }

    public function setFieldOption(string $key, mixed $value): void
    {
        $this->options['field_options'][$key] = $value;
    }

    public function getLabel(): string
    {
        return (string) $this->getOption('label');
    }

    public function setLabel(string $label): void
    {
        $this->setOption('label', $label);
    }

    public function getFormOptions(): array
    {
        return [
            'field_type' => $this->getFieldType(),
            'field_options' => $this->getFieldOptions(),
            'label' => $this->getLabel(),
        ];
    }
}
