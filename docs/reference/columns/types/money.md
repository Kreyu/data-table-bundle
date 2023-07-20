---
label: Money
order: c
---

# Money column type

The `MoneyColumnType` represents a column with monetary value, appropriately formatted and rendered with currency sign.

+-------------+---------------------------------------------------------------------+
| Parent type | [NumberType](number.md)
+-------------+---------------------------------------------------------------------+
| Class       | [:icon-mark-github: MoneyColumnType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/MoneyColumnType.php)
+-------------+---------------------------------------------------------------------+

## Options

### `currency`

- **type**: `string` or `callable` - any [3-letter ISO 4217 code](https://en.wikipedia.org/wiki/ISO_4217) 

Specifies the currency that the money is being specified in. 
This determines the currency symbol that should be shown in the column.

When using the [Intl number formatter](https://www.php.net/manual/en/class.numberformatter.php), 
the ISO code will be automatically converted to the appropriate currency sign, for example:

- `EUR` becomes `€`;
- `PLN` becomes `zł`;

Please note that the end result is also dependent on the locale used in the application, for example, with value of `100`: 

- `USD` currency will be rendered as `$100` when using the `en` locale;
- `USD` currency will be rendered as `100 USD` when using the `pl` locale;

When the Intl formatter is **NOT** used, given currency is simply rendered after the monetary value.

Additionally, the option accepts a callable, which gets a row data as first argument:

```php
$builder
    ->addColumn('price', MoneyColumnType::class, [
        'currency' => fn (Product $product) => $product->getPriceCurrency(),
    ])
;
```

### `use_intl_formatter`

- **type**: `bool`
- **default**: `true` if either [`symfony/intl`](https://packagist.org/packages/symfony/intl) or [`twig/intl-extra`](https://packagist.org/packages/twig/intl-extra) is installed

Determines whether the [Intl number formatter](https://www.php.net/manual/en/class.numberformatter.php) should be used.
Enabling this option will automatically handle the formatting based on the locale set in the application.
For example, value `123456.78` will be rendered differently:

- `123,456.78` when using `en` locale;
- `123 456,78` when using `pl` locale;
- etc.

!!! Note
When using Twig, enabling the Intl formatter without [`twig/intl-extra`](https://packagist.org/packages/twig/intl-extra) installed will result in an exception:

> The "format_currency" filter is part of the IntlExtension, which is not installed/enabled; try running "composer require twig/intl-extra"
!!!

### `intl_formatter_options`

- **type**: `array`
- **default**: `['attrs' => [], 'style' => 'decimal']`

Configures the [Intl number formatter](https://www.php.net/manual/en/class.numberformatter.php) if used.
For example, to limit decimal places to two:

```php
$builder
    ->addColumn('price', MoneyColumnType::class, [
        'intl_formatter_options' => [
            'attrs' => [
                // https://www.php.net/manual/en/class.numberformatter.php#numberformatter.constants.max-fraction-digits
                'max_fraction_digit' => 2,
            ],
        ])
    ])
;
```

For more details, see:
- [Intl number formatter documentation](https://www.php.net/manual/en/class.numberformatter.php)
- [Twig `format_currency` filter documentation](https://twig.symfony.com/doc/2.x/filters/format_currency.html)

## Inherited options

{{ include '_column_options' }}
