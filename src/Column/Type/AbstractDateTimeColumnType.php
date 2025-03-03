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

        // When exporting, we ensure the export "formatter" option is present, so the value gets pre-formatted.
        $resolver->addNormalizer('export', function (Options $options, mixed $export) {
            if (false === $export) {
                return false;
            }

            if (true === $export) {
                $export = [];
            }

            $export['formatter'] ??= $options['formatter'] ?? function (mixed $value) use ($options) {
                if (!$value instanceof \DateTimeInterface) {
                    return '';
                }

                $timezone = $options['timezone'];

                if (null === $timezone) {
                    $timezone = date_default_timezone_get();
                }

                if (is_string($timezone)) {
                    $timezone = new \DateTimeZone($timezone);
                }

                $dateTime = \DateTime::createFromInterface($value);

                if ($timezone instanceof \DateTimeZone) {
                    $dateTime->setTimezone($timezone);
                }

                return $dateTime->format($options['format']);
            };

            return $export;
        });
    }
}
