<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DateTimeColumnType extends AbstractColumnType
{
    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $view->vars = array_replace($view->vars, [
            'format' => $options['format'],
            'timezone' => $options['timezone'],
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
                'format' => 'd.m.Y H:i:s',
                'timezone' => null,
                'badge' => false,
            ])
            ->setNormalizer('export', function (Options $options, $value) {
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
            })
            ->setAllowedTypes('format', ['string'])
            ->setAllowedTypes('timezone', ['null', 'string'])
            ->setAllowedTypes('badge', ['bool', 'string', 'callable'])
            ->setInfo('format', 'A date time string format, supported by the PHP date() function.')
            ->setInfo('timezone', 'A timezone used to render the date time as string.')
            ->setInfo('badge', 'Defines whether the value should be rendered as a badge. Can be a boolean, string, or callable.')
        ;
    }

    public function getParent(): ?string
    {
        return TextColumnType::class;
    }
}
