<script setup>
    import ColumnTypeOptions from "./options/column.md";
</script>

# IconColumnType

The [`IconColumnType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/IconColumnType.php) represents a column rendered as an icon.

## Options

### `icon`

- **type**: `string` or `\Closure`

Defines the icon to render.

```php
use Kreyu\Bundle\DataTableBundle\Column\Type\IconColumnType;

$builder
    ->addColumn('status', IconColumnType::class, [
        'icon' => 'check',
    ])
;
```

> [!TIP] Wondering how does the icon gets rendered?
> Name of the icon depends on the icon set you are using in the application,
> and which icon theme is configured for the data table. See the [icon themes documentation section](./../../../docs/features/theming.md#icon-themes) for more information.

You can provide a closure that will receive a column value as an argument:

```php
use Kreyu\Bundle\DataTableBundle\Column\Type\IconColumnType;

$builder
    ->addColumn('status', IconColumnType::class, [
        'icon' => fn (string $status) => match ($status) {
            'draft' => 'clock',
            'completed' => 'check',
        },
    ])
;
```

### `icon_attr`

- **type**: `array` or `\Closure`
- **default**: `[]`

Defines the HTML attributes for the icon to render.

```php
use Kreyu\Bundle\DataTableBundle\Column\Type\IconColumnType;

$builder
    ->addColumn('status', IconColumnType::class, [
        'icon' => 'check',
        'icon_attr' => [
            'class' => 'text-success',
        ],
    ])
;
```

You can provide a closure that will receive a column value as an argument:

```php
use Kreyu\Bundle\DataTableBundle\Column\Type\IconColumnType;

$builder
    ->addColumn('status', IconColumnType::class, [
        'icon' => fn (string $status) => match ($status) {
            'draft' => 'clock',
            'completed' => 'check',
        },
        'icon_attr' => fn (string $status) => [
            'class' => match ($status) {
                'draft' => 'text-warning',
                'completed' => 'text-success',
            },
        ],
    ])
;
```

## Inherited options

<ColumnTypeOptions/>
