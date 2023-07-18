Batch actions are special actions that hold reference to the user-selected rows.

## Prerequisites

To begin with, make sure the [Symfony UX integration is enabled](../installation.md#enable-the-symfony-ux-integration).
Then, enable the `batch` controller:

```json # assets/controllers.json
{
    "controllers": {
        "@kreyu/data-table-bundle": {
            "batch": {
                "enabled": true
            }
        }
    }
}
```

## Adding batch actions

To add batch action, use data table builder's `addBatchAction()` method:

```php # src/DataTable/Type/ProductDataTableType.php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder->addBatchAction('settle', ButtonActionType::class, [
            'href' => '/products/settle',
        ]);
    }
}
```

The builder's `addBatchAction()` method accepts *three* arguments:

- action name;
- action type - with a fully qualified class name;
- action options - defined by the action type, used to configure the action;

For reference, see [built-in action types](../reference/actions/types.md).

## Adding checkbox column type

Batch actions require the user to select specific rows. To help with that process, if at least one batch action is defined, a CheckboxColumnType will be added automatically.

This column will be named `__batch`, which can be referenced using the constant:

```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;

$column = $builder->getColumn(DataTableBuilderInterface::BATCH_CHECKBOX_COLUMN_NAME);
```

This behavior can be disabled (or enabled back again) using the builder's method:

```php
$builder->setAutoAddingBatchCheckboxColumn(false);
```

## Changing the identifier property path

By default, the checkbox column type will try to retrieve the identifier on the `id` property path. This can be changed similarly to other column types, by providing the `property_path` option:

```php
$builder->addColumn('__batch', CheckboxColumnType::class, [
    'property_path' => 'product.id',
])
```

## Changing the identifier query parameter name

By default, the checkbox column type will add the `id` parameter to the batch actions.
For example, checking rows with ID 1, 2 will result in:

- every batch action's `href` parameter appended with `id[]=1&id[]=2`
- every batch action's `data-id` parameter set to `[1,2]`

The parameter name can be changed by providing the `identifier_name` option:

```php
$builder->addColumn('__batch', CheckboxColumnType::class, [
    'identifier_name' => 'product_id',
]);
```

Using the above configuration, checking rows with ID 1, 2 will result in:

- every batch action's `href` parameter appended with `product_id[]=1&product_id[]=2`
- every batch action's `data-product-id` parameter set to `[1,2,3]`

If the action has no `href` parameter, the query parameters will not be appended.
The data parameters are not used internally and can be used for custom scripts.

## Adding multiple checkbox columns

Using multiple checkbox columns for a single data table is supported.

For example, using the following configuration:

```php # src/DataTable/Type/ProductDataTableType.php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Column\Type\CheckboxColumnType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addColumn('productId', CheckboxColumnType::class, [
                'property_path' => 'id',
                'identifier_name' => 'product_id',
            ])
            ->addColumn('categoryId', CheckboxColumnType::class, [
                'property_path' => 'category.id',
                'identifier_name' => 'category_id',
            ])
        ;
    }
}
```

And having a data set which consists of two rows:

| Product ID | Category ID |
|------------|-------------|
| 1          | 3           |
| 2          | 4           |

Checking the first row's product and second row's category will result in:

- every batch action's `href` parameter appended with `product_id[]=1&category_id[]=4`
- every batch action's `data-product-id` parameter set to `[1]` and `data-category-id` set to `[4]`
