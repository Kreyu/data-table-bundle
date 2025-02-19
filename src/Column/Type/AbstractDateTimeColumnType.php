<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractDateTimeColumnType extends AbstractColumnType
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
        $resolver->define('format')
            ->allowedTypes('string')
            ->info('A date time string format, supported by the PHP date() function - null to use default.')
        ;

        $resolver->define('timezone')
            ->default(null)
            ->allowedTypes('null', 'bool', 'string', \DateTimeZone::class)
            ->info('Target timezone - null to use the default, false to leave unchanged.')
        ;

        $resolver->setNormalizer('export', function (Options $options, $value) {
            if (true === $value) {
                $value = [];
            }

            if (is_array($value)) {
                $value += [
                    'formatter' => static function (mixed $value, mixed $data, ColumnInterface $column): string {
                        if ($value instanceof \DateTimeInterface) {
                            return $value->format($column->getConfig()->getOption('format'));
                        }

                        return '';
                    },
                ];
            }

            return $value;
        });
    }
}
