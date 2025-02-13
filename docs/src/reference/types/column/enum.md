<script setup>
    import ColumnTypeOptions from "./options/column.md";
</script>

# EnumColumnType

The [`EnumColumnType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/EnumColumnType.php) represents a column with [PHP enumeration](https://www.php.net/manual/language.enumerations.php) as value.

## Overridden options

### `formatter`

- **type**: `null` or `callable`
- **default**: callable that translates the enum if possible

Formats the enum value. If Symfony Translator component is available, and the enum implements [`TranslatableInterface`](https://github.com/symfony/translation-contracts/blob/main/TranslatableInterface.php),
the enum will be translated. Otherwise, the enum name will be displayed.

### `badge`

- **type**: `bool`, `string`, or `callable`
- **default**: `false`

Defines whether the value should be rendered as a badge. Can be a boolean, string, or callable.

Example usage:

```php
$builder
    ->addColumn('status', EnumColumnType::class, [
        'badge' => 'primary',
    ])
;
```

## Inherited options

<ColumnTypeOptions excludedOptions="['formatter']"/>
