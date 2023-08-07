<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\DataTransformer\MoneyToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MoneyColumnType extends AbstractColumnType
{
    protected static array $patterns = [];

    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $callableOptions = [
            'currency',
            'divisor',
        ];

        foreach ($callableOptions as $optionName) {
            if (is_callable($options[$optionName])) {
                $options[$optionName] = $options[$optionName]($view->parent->data);
            }
        }

        $view->vars = array_merge($view->vars, [
            'currency' => $options['currency'],
            'divisor' => $options['divisor'],
            'money_pattern' => self::getPattern($options['currency']),
        ]);

        $transformer = new MoneyToLocalizedStringTransformer(
            scale: $options['scale'],
            roundingMode: $options['rounding_mode'],
            decimalSeparatorSymbol: $options['decimal_separator'],
            thousandsSeparatorSymbol: $options['thousands_separator'],
            divisor: $options['divisor'],
        );

        // $view->vars['value'] = $view->value = $transformer->transform($view->value);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'currency' => 'EUR',
                'divisor' => 1,
                'scale' => 2,
            ])
            ->setAllowedTypes('currency', ['string', 'callable'])
            ->setAllowedTypes('divisor', ['int', 'callable'])
        ;
    }

    public function getParent(): ?string
    {
        return NumberColumnType::class;
    }

    /**
     * Returns the pattern for this locale in UTF-8.
     *
     * The pattern contains the placeholder "{{ widget }}" where the HTML tag should be inserted.
     *
     * Implementation copied from {@see MoneyType}.
     */
    protected static function getPattern(?string $currency)
    {
        if (!$currency) {
            return '{{ widget }}';
        }

        $locale = \Locale::getDefault();

        if (!isset(self::$patterns[$locale])) {
            self::$patterns[$locale] = [];
        }

        if (!isset(self::$patterns[$locale][$currency])) {
            $format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
            $pattern = $format->formatCurrency(123, $currency);

            // the spacings between currency symbol and number are ignored, because
            // a single space leads to better readability in combination with input
            // fields

            // the regex also considers non-break spaces (0xC2 or 0xA0 in UTF-8)

            preg_match('/^([^\s\xc2\xa0]*)[\s\xc2\xa0]*123(?:[,.]0+)?[\s\xc2\xa0]*([^\s\xc2\xa0]*)$/u', $pattern, $matches);

            if (!empty($matches[1])) {
                self::$patterns[$locale][$currency] = $matches[1].' {{ widget }}';
            } elseif (!empty($matches[2])) {
                self::$patterns[$locale][$currency] = '{{ widget }} '.$matches[2];
            } else {
                self::$patterns[$locale][$currency] = '{{ widget }}';
            }
        }

        return self::$patterns[$locale][$currency];
    }
}
