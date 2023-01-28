<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectionType extends AbstractType implements ColumnFactoryAwareInterface
{
    private ?ColumnFactoryInterface $columnFactory = null;

    public function buildView(ColumnView $view, ColumnInterface $column, array $options): void
    {
        $view->vars['children'] = [];

        foreach ($view->vars['value'] ?? [] as $index => $data) {
            $child = $this->columnFactory->create(
                $column->getName() . '__' . ($index + 1),
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
                'entry_type' => TextType::class,
                'entry_options' => [],
                'separator' => ',',
            ])
            ->setAllowedTypes('entry_type', ['string'])
            ->setAllowedTypes('entry_options', ['array'])
            ->setAllowedTypes('separator', ['null', 'string'])
        ;
    }

    public function setColumnFactory(ColumnFactoryInterface $columnFactory): void
    {
        $this->columnFactory = $columnFactory;
    }
}
