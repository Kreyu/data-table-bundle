<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\Factory\ColumnFactoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectionType extends AbstractType implements ColumnFactoryAwareTypeInterface
{
    private ?ColumnFactoryInterface $columnFactory = null;

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults([
                'entry_type' => TextType::class,
                'entry_options' => [],
                'prototype' => null,
            ])
            ->setAllowedTypes('entry_type', ['string'])
            ->addNormalizer('prototype', function (Options $options): ?ColumnInterface {
                return $this->columnFactory?->create('_', $options['entry_type'], $options['entry_options']);
            });
    }

    public function setColumnFactory(ColumnFactoryInterface $columnFactory): void
    {
        $this->columnFactory = $columnFactory;
    }
}
