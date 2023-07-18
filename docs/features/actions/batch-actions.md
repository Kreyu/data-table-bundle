---
order: b
---

# Batch actions

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

The same method can also be used on already created data tables:

```php #20-22 src/Controller/ProductController.php
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function index(Request $request)
    {
        $dataTable = $this->createDataTable(ProductDataTableType::class);
        
        $dataTable->addBatchAction('settle', ButtonActionType::class, [
            'href' => '/products/settle',
        ]);
    }
}
```

The builder's `addBatchAction()` method accepts *three* arguments:

- action name;
- action type - with a fully qualified class name;
- action options - defined by the action type, used to configure the action;

For reference, see [built-in action types](../../reference/actions/types.md).

## Adding checkbox column

Batch actions require the user to select specific rows. This is handled by the CheckboxColumnType, 
which simply renders a checkbox with value set to row identifier. To help with that process, 
if at least one batch action is defined, this checkbox column will be added automatically.

This column will be named `__batch`, which can be referenced using the constant:

```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;

$column = $builder->getColumn(DataTableBuilderInterface::BATCH_CHECKBOX_COLUMN_NAME);
```

This behavior can be disabled (or enabled back again) using the builder's method:

```php
$builder->setAutoAddingBatchCheckboxColumn(false);
```

## Removing batch actions

To remove existing batch action, use the builder's `removeBatchAction()` method:

```php #14 src/DataTable/Type/ProductDataTableType.php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProductDataTableType extends AbstractDataTableType
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }
    
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder->removeBatchAction('settle');
    }
}
```

The same method can also be used on already created data tables:

```php #14 src/Controller/ProductController.php
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    
    public function index(Request $request)
    {
        $dataTable = $this->createDataTable(ProductDataTableType::class);
        
        $dataTable->removeBatchAction('settle');
    }
}
```

Any attempt of removing the non-existent batch action will silently fail.

## Retrieving batch actions

To retrieve already defined batch actions, use the builder's `getBatchActions()` or `getBatchAction()` method:

```php # src/DataTable/Type/ProductDataTableType.php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        // retrieve all previously defined batch actions:
        $actions = $builder->getBatchActions();
        
        // or specific batch action:
        $action = $builder->getBatchAction('settle');
        
        // or simply check whether the batch action is defined:
        if ($builder->hasBatchAction('settle')) {
            // ...
        }
    }
}
```

The same methods are accessible on already created data tables:

```php # src/Controller/ProductController.php
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    
    public function index(Request $request)
    {
        $dataTable = $this->createDataTable(ProductDataTableType::class);
        
        // retrieve all previously defined batch actions:
        $actions = $dataTable->getBatchActions();
        
        // or specific batch action:
        $action = $dataTable->getBatchAction('create');
        
        // or simply check whether the batch action is defined:
        if ($dataTable->hasBatchAction('create')) {
            // ...
        }
    }
}
```

!!!warning Warning
Any attempt of retrieving a non-existent action will result in an `OutOfBoundsException`.  
To check whether the batch action of given name exists, use the `hasBatchAction()` method.
!!!

!!!danger Important
Within the data table builder, the actions are still in their build state!
Therefore, actions retrieved by the methods:

- `DataTableBuilderInterface::getBatchActions()`
- `DataTableBuilderInterface::getBatchAction(string $name)`

...are instance of `ActionBuilderInterface`, whereas methods:

- `DataTableInterface::getBatchActions()`
- `DataTableInterface::getBatchAction(string $name)`

...return instances of `ActionInterface` instead.
!!!

## Changing the identifier property

By default, the checkbox column type will try to retrieve the identifier on the `id` property path. 
This can be changed similarly to other column types, by providing the `property_path` option:

```php
$builder->addColumn('__batch', CheckboxColumnType::class, [
    'property_path' => 'uuid',
]);
```

If property accessor is not enough, use the `getter` option:

```php
$builder->addColumn('__batch', CheckboxColumnType::class, [
    'getter' => fn (Product $product) => $product->getUuid(),
]);
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