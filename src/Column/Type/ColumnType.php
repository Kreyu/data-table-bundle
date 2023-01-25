<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Translation\TranslatableMessage;

final class ColumnType implements ColumnTypeInterface
{
    public function buildView(ColumnView $view, ColumnInterface $column, array $options): void
    {
        $data = $column->getData();

        if (null !== $data) {
            $options['data'] = $data;
        }

        foreach ($options as $key => $value) {
            $view->vars[$key] = $value;
        }

        if (is_array($data) || is_object($data)) {
            $propertyPath = $options['property_path'] ?? $column->getName();
            $propertyAccessor = PropertyAccess::createPropertyAccessor();

            $propertyData = $propertyAccessor->getValue($data, $propertyPath);

            foreach ($view->vars as $key => $value) {
                if (is_callable($value)) {
                    $view->vars[$key] = $value($propertyData);
                }
            }

            $view->vars['data'] = $propertyData;
        }

        $view->vars['data_table'] = $view->parent;
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
                'block_name' => 'data_table_'.$this->getBlockPrefix(),
                'block_prefix' => $this->getBlockPrefix(),
                'value' => null,
                'display_personalization_button' => false,
            ])
            ->setAllowedTypes('label', ['null', 'string', TranslatableMessage::class])
            ->setAllowedTypes('label_translation_parameters', ['array', 'callable'])
            ->setAllowedTypes('translation_domain', ['bool', 'string'])
            ->setAllowedTypes('property_path', ['null', 'bool', 'string'])
            ->setAllowedTypes('sort_field', ['bool', 'string'])
            ->setAllowedTypes('block_name', ['null', 'string'])
            ->setAllowedTypes('block_prefix', ['null', 'string'])
            ->setAllowedTypes('display_personalization_button', ['bool'])
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
}