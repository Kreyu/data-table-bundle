<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatableInterface;

final class BooleanColumnType extends AbstractColumnType
{
    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $view->vars = array_replace($view->vars, [
            'label_true' => $options['label_true'],
            'label_false' => $options['label_false'],
            'badge' => $options['badge'],
        ]);

        if ($options['badge']) {
            $badgeClass = is_callable($options['badge']) ? $options['badge']($view->data) : $options['badge'];
            $view->vars['attr']['class'] = trim(($view->vars['attr']['class'] ?? '') . ' badge ' . $badgeClass);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'label_true' => 'Yes',
                'label_false' => 'No',
                'value_translation_domain' => 'KreyuDataTable',
                'badge' => false,
            ])
            ->setAllowedTypes('label_true', ['string', TranslatableInterface::class])
            ->setAllowedTypes('label_false', ['string', TranslatableInterface::class])
            ->setAllowedTypes('badge', ['bool', 'string', 'callable'])
            ->setInfo('label_true', 'Label displayed when the value equals true.')
            ->setInfo('label_false', 'Label displayed when the value equals false.')
            ->setInfo('badge', 'Defines whether the value should be rendered as a badge. Can be a boolean, string, or callable.')
        ;
    }
}
