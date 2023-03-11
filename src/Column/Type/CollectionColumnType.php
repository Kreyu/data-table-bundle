<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryAwareInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryAwareTrait;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectionColumnType extends AbstractColumnType implements ColumnFactoryAwareInterface
{
    use ColumnFactoryAwareTrait;

    public function buildView(ColumnView $view, ColumnInterface $column, array $options): void
    {
        $view->vars['children'] = [];

        foreach ($view->vars['value'] ?? [] as $index => $data) {
            $child = $this->columnFactory->create(
                $column->getName().'__'.($index + 1),
                $options['entry_type'],
                $options['entry_options'],
            );

            $child->setData($data);

            $view->vars['children'][] = $child->createView($view->parent);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'entry_type' => TextColumnType::class,
                'entry_options' => [],
                'separator' => ',',
            ])
            ->setAllowedTypes('entry_type', ['string'])
            ->setAllowedTypes('entry_options', ['array'])
            ->setAllowedTypes('separator', ['null', 'string'])
            ->setNormalizer('entry_options', function (Options $options, array $value): array {
                return $value + ['property_path' => false];
            })
            ->setNormalizer('non_resolvable_options', function (Options $options, array $value): array {
                if (!in_array('entry_options', $value)) {
                    $value[] = 'entry_options';
                }

                return $value;
            })
        ;
    }
}
