<script setup>
    import ColumnTypeOptions from "./options/column.md";
</script>

# MoneyColumnType

The [`MoneyColumnType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/MoneyColumnType.php) represents a column with monetary value, appropriately formatted and rendered with currency sign.

## Options

### `currency`

- **type**: `string` or `\Closure` - any [3-letter ISO 4217 code](https://en.wikipedia.org/wiki/ISO_4217)

Specifies the currency that the money is being specified in.
This determines the currency symbol that should be shown in the column.

When using the [Intl number formatter](https://www.php.net/manual/en/class.numberformatter.php),
the ISO code will be automatically converted to the appropriate currency sign, for example:

- `EUR` becomes `€`;
- `PLN` becomes `zł`;

Please note that the end result is also dependent on the locale used in the application, for example, with value of `1000`:

- `USD` currency will be rendered as `$1,000.00` when using the `en` locale;
- `USD` currency will be rendered as `1 000,00 USD` when using the `pl` locale;

When the Intl formatter is **NOT** used, given currency is simply rendered after the raw value, e.g. `1000 USD`.

Additionally, the option accepts a closure, which gets a row data as first argument:

```php
$builder
    ->addColumn('price', MoneyColumnType::class, [
        'currency' => fn (Product $product) => $product->getPriceCurrency(),
    ])
;
```

### `divisor`

- **type**: `integer`
- **default**: `1`

If you need to divide your starting value by a number before rendering it to the user,
you can use the `divisor` option. For example if you need to show amounts as integer in order to avoid
rounding errors, you can transform values in cents automatically:

```php
$builder
    ->addColumn('price', MoneyColumnType::class, [
        'divisor' => 100,
    ])
;
```

In this case, if the price field is set to 9950, then the value 99.5 will actually be rendered to the user.

### `use_intl_formatter`

- **type**: `bool`
- **default**: `true` if [`symfony/intl`](https://packagist.org/packages/symfony/intl) is installed, `false` instead

Determines whether the [Intl number formatter](https://www.php.net/manual/en/class.numberformatter.php) should be used.
Enabling this option will automatically handle the formatting based on the locale set in the application.
For example, value `123456.78` will be rendered differently:

- `123,456.78` when using `en` locale;
- `123 456,78` when using `pl` locale;
- etc.

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

<ColumnTypeOptions/>
