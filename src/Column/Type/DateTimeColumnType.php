<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateTimeColumnType extends AbstractColumnType
{
    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $view->vars = array_replace($view->vars, [
            'format' => $options['format'],
            'timezone' => $options['timezone'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'format' => 'd.m.Y H:i:s',
                'timezone' => null,
            ])
            ->setAllowedTypes('format', ['string'])
            ->setAllowedTypes('timezone', ['null', 'string'])
            ->setInfo('format', 'A date time string format, supported by the PHP date() function.')
            ->setInfo('timezone', 'A timezone used to render the date time as string.')
        ;
    }

    public function getParent(): ?string
    {
        return TextColumnType::class;
    }
}
