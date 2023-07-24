---
label: Number
order: b
---

# Number column type

The `NumberColumnType` represents a column with value displayed as a number.

+-------------+---------------------------------------------------------------------+
| Parent type | [ColumnType](column)
+-------------+---------------------------------------------------------------------+
| Class       | [:icon-mark-github: NumberColumnType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/NumberColumnType.php)
+-------------+---------------------------------------------------------------------+

## Options

### `use_intl_formatter`

- **type**: `bool`
- **default**: `true` if [`symfony/intl`](https://packagist.org/packages/symfony/intl), is installed `false` instead

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
- [Twig `format_number` filter documentation](https://twig.symfony.com/doc/2.x/filters/format_number.html)

## Inherited options

{{ include '_column_options' }}
