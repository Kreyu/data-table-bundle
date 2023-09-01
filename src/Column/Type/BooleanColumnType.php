<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatableInterface;

class BooleanColumnType extends AbstractColumnType
{
    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $view->vars = array_replace($view->vars, [
            'label_true' => $options['label_true'],
            'label_false' => $options['label_false'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'label_true' => 'Yes',
                'label_false' => 'No',
                'value_translation_domain' => 'KreyuDataTable',
            ])
            ->setAllowedTypes('label_true', ['string', TranslatableInterface::class])
            ->setAllowedTypes('label_false', ['string', TranslatableInterface::class])
            ->setInfo('label_true', 'Label displayed when the value equals true.')
            ->setInfo('label_false', 'Label displayed when the value equals false.')
        ;
    }
}
