# ActionsColumnType

The [:material-github: ActionsColumnType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/ActionsColumnType.php) represents a column with value displayed as a list of actions.

## Options

### `actions`

**type**: `array` **default**: `[]`

This option contains a list of actions. Each actions consists of two options:

- `type` (**type**: `string`) - FQCN of the action type class;
- `type_options` (**type**: `array`, **default**: `[]`) - options passed to the action type;

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
                    'label' => t('Details'),
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

{% include-markdown "_column_options.md" heading-offset=2 %}
