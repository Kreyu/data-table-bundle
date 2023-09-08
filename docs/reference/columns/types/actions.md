---
label: Actions
order: j
tags:
  - columns
---

# Actions column type

The `ActionsColumnType` represents a column that contains row actions.

+-------------+---------------------------------------------------------------------+
| Parent type | [ColumnType](column)
+-------------+---------------------------------------------------------------------+
| Class       | [:icon-mark-github: ActionsColumnType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/ActionsColumnType.php)
+-------------+---------------------------------------------------------------------+

## Options

### `actions`

- **type**: `array`
- **default**: `[]`

This option contains a list of actions. Each action consists of _three_ options:

:::
- `type`   
  - **type**: `string`  
  Fully qualified class name of the [action type](../../../reference/actions/types.md).
:::
:::
- `type_options`
  - **type**: `array`
  - **default**: `[]`  
  Options passed to the action type.
::: 
:::
- `visible`   
  - **type**: `bool`or `callable`
  - **default**: `true`  
    Determines whether the action should be visible.
:::

Example usage:

```php #
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

{{ include '_column_options' }}
