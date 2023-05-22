---
label: Actions
order: j
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

- **array**: `array`
- **default**: `[]`

This option contains a list of actions. Each action consists of two options:

- `type` (**type**: `string`) - fully qualified class name of the action type;
- `type_options` (**type**: `array`, **default**: `[]`) - options passed to the action type;

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
            ],
        ],
    ])
;
```

## Inherited options

{{ include '_column_options' }}
