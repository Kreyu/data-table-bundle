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
        $resolver
            ->define('format')
            ->allowedTypes('string')
            ->info('A date time string format, supported by the PHP date() function - null to use default.')
        ;

        $resolver
            ->define('timezone')
            ->default(null)
            ->allowedTypes('null', 'bool', 'string', \DateTimeZone::class)
            ->info('Target timezone - null to use the default, false to leave unchanged.')
        ;

        $resolver->addNormalizer(
            'export',
            /**
             * @param bool|array<array-key, mixed> $export
             *
             * @return false|array<array-key, mixed>
             */
            static function (Options $options, bool|array $export): false|array {
                if ($export === false) {
                    return false;
                }

                if ($export === true) {
                    $export = [];
                }

                $export['formatter'] ??= $options['formatter'] ?? static function (mixed $value) use (
                    $options,
                ): string {
                    if (!$value instanceof \DateTimeInterface) {
                        return '';
                    }

                    /** @var bool|string|\DateTimeZone|null $timezone */
                    $timezone = $options['timezone'];

                    if ($timezone === null) {
                        $timezone = date_default_timezone_get();
                    }

                    if (is_string($timezone)) {
                        $timezone = new \DateTimeZone($timezone);
                    }

                    $dateTime = \DateTime::createFromInterface($value);

                    if ($timezone instanceof \DateTimeZone) {
                        $dateTime->setTimezone($timezone);
                    }

                    /** @var string $format */
                    $format = $options['format'];

                    return $dateTime->format($format);
                };

                return $export;
            },
        );
    }
}
