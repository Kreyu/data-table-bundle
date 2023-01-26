<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPathInterface;
use Symfony\Component\Translation\TranslatableMessage;

final class ColumnType implements ColumnTypeInterface
{
    public function buildView(ColumnView $view, ColumnInterface $column, array $options): void
    {
        $resolver = clone $column->getType()->getOptionsResolver();

        $resolver
            ->setDefaults([
                'data' => $column->getData(),
                'value' => $column->getData(),
                'property_path' => $column->getName(),
                'block_prefix' => $column->getType()->getBlockPrefix(),
                'block_name' => 'data_table_' . $column->getType()->getBlockPrefix(),
            ])
        ;

        $options = $resolver->resolve(array_filter($options));

        $value = $options['value'];
        $formatter = $options['formatter'];
        $propertyPath = $options['property_path'];
        $propertyAccessor = $options['property_accessor'];

        if (false !== $propertyPath && (is_array($value) || is_object($value))) {
            if ($propertyAccessor->isReadable($value, $propertyPath)) {
                $value = $propertyAccessor->getValue($value, $propertyPath);
            }
        }

        if (is_callable($formatter)) {
            $options['value'] = $formatter($value);
        } else {
            $options['value'] = $value;
        }

        $normalizableOptions = array_diff_key($options, ['formatter' => true]);

        $view->vars = $this->normalizeOptions($normalizableOptions, $value);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'label' => null,
                'label_translation_parameters' => [],
                'translation_domain' => 'KreyuDataTable',
                'property_path' => null,
                'sort_field' => false,
                'block_name' => null,
                'block_prefix' => null,
                'value' => null,
                'display_personalization_button' => false,
                'property_accessor' => PropertyAccess::createPropertyAccessor(),
                'formatter' => null,
            ])
            ->setAllowedTypes('label', ['null', 'string', TranslatableMessage::class])
            ->setAllowedTypes('label_translation_parameters', ['array', 'callable'])
            ->setAllowedTypes('translation_domain', ['bool', 'string'])
            ->setAllowedTypes('property_path', ['null', 'bool', 'string', PropertyPathInterface::class])
            ->setAllowedTypes('sort_field', ['bool', 'string'])
            ->setAllowedTypes('block_name', ['null', 'string'])
            ->setAllowedTypes('block_prefix', ['null', 'string'])
            ->setAllowedTypes('display_personalization_button', ['bool'])
            ->setAllowedTypes('property_accessor', [PropertyAccessorInterface::class])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'column';
    }

    public function getParent(): ?string
    {
        return null;
    }

    private function normalizeOptions(array $options, mixed $value): array
    {
        foreach ($options as $key => $option) {
            if (is_array($option)) {
                $option = $this->normalizeOptions($option, $value);
            }

            if ($option instanceof \Closure) {
                $option = $option($value);
            }

            $options[$key] = $option;
        }

        return $options;
    }
}