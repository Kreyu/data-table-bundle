<script setup>
    import ColumnTypeOptions from "./options/column.md";
</script>

# ActionsColumnType

The [`ActionsColumnType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/ActionsColumnType.php) represents a column that contains row actions.

::: info In most cases, it is not necessary to use this column type directly.
Instead, use data table builder's `addRowAction()` method.
If at least one row action is defined and is visible, an `ActionColumnType` is added to the data table.
:::

## Options

### `actions`

- **type**: `array`
- **default**: `[]`

This option contains a list of actions. Each action consists of _three_ options:

> #### `type`
>
> - **type**: `string`
>
> Fully qualified class name of the [action type](#).
> <br/><br/>
>
> #### `type_options`
>
> - **type**: `array`
> - **default**: `[]`
>
> Options passed to the action type.
> <br/><br/>
>
> #### `visible`
>
> - **type**: `bool`or `\Closure`
> - **default**: `true`

Determines whether the action should be visible.

Example usage:

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ActionsColumnType;

$builder
    ->addColumn('actions', ActionsColumnType::class, [
        'actions' => [
            'show' => [
                'type' => ButtonActionType::class,
                'type_options' => [
                    'url' => function (Product $product): string {
                        return $this->urlGenerator->generate('category_show', [
                            'id' => $product->getId(),
                        ]);
                    }),
                ],
                'visible' => function (Product $product): bool {
                    return $product->isActive();
                }
            ],
        ],
    ])
;
```

## Inherited options

<ColumnTypeOptions/>
