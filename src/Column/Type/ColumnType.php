<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
                'label' => ucfirst($column->getName()),
                'translation_domain' => $view->parent->vars['label_translation_domain'],
                'data' => $column->getData(),
                'value' => $column->getData(),
                'property_path' => $column->getName(),
                'block_prefix' => $column->getType()->getBlockPrefix(),
                'block_name' => 'kreyu_data_table_column_'.$column->getType()->getBlockPrefix(),
            ])
            ->setNormalizer('sort', function (Options $options, mixed $value) use ($column) {
                if (true === $value) {
                    return $column->getName();
                }

                return $value;
            })
        ;

        $options = $resolver->resolve(array_filter($options, fn ($option) => null !== $option));
        $options['sort_field'] = $options['sort'];

        unset($options['sort']);

        $value = $options['value'];
        $formatter = $options['formatter'];
        $propertyPath = $options['property_path'];
        $propertyAccessor = $options['property_accessor'];

        if (false !== $propertyPath && (is_array($value) || is_object($value))) {
            if ($propertyAccessor->isReadable($value, $propertyPath)) {
                $value = $propertyAccessor->getValue($value, $propertyPath);
            }
        }

        if (null !== $column->getData()) {
            $options['value'] = $value;

            if (is_callable($formatter)) {
                $options['value'] = $formatter($value);
            }

            $options = array_merge($options, $this->normalizeOptions(
                array_diff_key($options, array_flip($options['non_normalizable_options'])),
                $value,
            ));
        }

        $options['data_table'] = $view->parent;

        $view->vars = $options;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'label' => null,
                'label_translation_parameters' => [],
                'translation_domain' => null,
                'property_path' => null,
                'sort' => false,
                'block_name' => null,
                'block_prefix' => null,
                'value' => null,
                'display_personalization_button' => false,
                'property_accessor' => PropertyAccess::createPropertyAccessor(),
                'formatter' => null,
                'exportable' => true,
                'non_normalizable_options' => [
                    'exportable',
                ],
            ])
            ->setAllowedTypes('label', ['null', 'string', TranslatableMessage::class])
            ->setAllowedTypes('label_translation_parameters', ['array', 'callable'])
            ->setAllowedTypes('translation_domain', ['null', 'bool', 'string'])
            ->setAllowedTypes('property_path', ['null', 'bool', 'string', PropertyPathInterface::class])
            ->setAllowedTypes('sort', ['bool', 'string'])
            ->setAllowedTypes('block_name', ['null', 'string'])
            ->setAllowedTypes('block_prefix', ['null', 'string'])
            ->setAllowedTypes('display_personalization_button', ['bool'])
            ->setAllowedTypes('property_accessor', [PropertyAccessorInterface::class])
            ->setAllowedTypes('formatter', ['null', 'callable'])
            ->setAllowedTypes('exportable', ['bool'])
            ->setAllowedTypes('non_normalizable_options', ['string[]'])
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
