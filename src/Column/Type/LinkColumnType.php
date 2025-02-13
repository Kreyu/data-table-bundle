<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class LinkColumnType extends AbstractColumnType
{
    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        if (is_callable($href = $options['href'])) {
            $href = $href($view->vars['data'], $view->parent->data, $column);
        }

        if (is_callable($target = $options['target'])) {
            $target = $target($view->vars['data'], $view->parent->data, $column);
        }

        $view->vars = array_replace($view->vars, [
            'href' => $href,
            'target' => $target,
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
                'href' => '#',
                'target' => null,
                'badge' => false,
            ])
            ->setAllowedTypes('href', ['string', 'callable'])
            ->setAllowedTypes('target', ['null', 'string', 'callable'])
            ->setAllowedTypes('badge', ['bool', 'string', 'callable'])
            ->setInfo('badge', 'Defines whether the value should be rendered as a badge. Can be a boolean, string, or callable.')
        ;
    }

    public function getParent(): ?string
    {
        return TextColumnType::class;
    }
}
