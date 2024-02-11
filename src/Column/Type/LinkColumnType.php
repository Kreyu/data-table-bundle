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
            $href = $href($view->vars['data']);
        }

        if (is_callable($target = $options['target'])) {
            $target = $target($view->vars['data']);
        }

        $view->vars = array_replace($view->vars, [
            'href' => $href,
            'target' => $target,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'href' => '#',
                'target' => '_self',
            ])
            ->setAllowedTypes('href', ['string', 'callable'])
            ->setAllowedTypes('target', ['string', 'callable'])
        ;
    }

    public function getParent(): ?string
    {
        return TextColumnType::class;
    }
}
