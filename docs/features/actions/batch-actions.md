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
use Kreyu\Bundle\DataTableBundle\Action\Type\FormActionType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder->addBatchAction('remove', FormActionType::class, [
            'action' => '/products',
            'method' => 'DELETE',
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
        
        $dataTable->addBatchAction('remove', FormActionType::class, [
            'action' => '/products',
            'method' => 'DELETE',
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
        $builder->removeBatchAction('remove');
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
        
        $dataTable->removeBatchAction('remove');
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
        $action = $builder->getBatchAction('remove');
        
        // or simply check whether the batch action is defined:
        if ($builder->hasBatchAction('remove')) {
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
        $action = $dataTable->getBatchAction('remove');
        
        // or simply check whether the batch action is defined:
        if ($dataTable->hasBatchAction('remove')) {
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
- every batch action's `data-product-id` parameter set to `[1,2]`

If the action has no `href` parameter, the query parameters will not be appended.
The data parameters are not used internally and can be used for custom scripts.

If `FormActionType` is used, the scripts will append hidden inputs with selected values, for example:

```html
<input type="hidden" name="id[]" value="1">
<input type="hidden" name="id[]" value="2">
```

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

If `FormActionType` is used, the scripts will append hidden inputs with selected values, for example:

```html
<input type="hidden" name="product_id[]" value="1">
<input type="hidden" name="category_id[]" value="4">
```

## Adding action confirmation

Actions can be configured to require confirmation (by the user) before being executed.

![Action confirmation modal with the Tabler theme](../../static/action_confirmation_modal.png)

To enable confirmation in the quickest way, set the action's `confirmation` option to `true`:

```php #10 src/DataTable/Type/ProductDataTableType.php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Action\Type\FormActionType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder->addBatchAction('remove', FormActionType::class, [
            'confirmation' => true,
        ]);
    }
}
```

To configure the confirmation modal, pass the array as the `confirmation` option:

```php #10-17 src/DataTable/Type/ProductDataTableType.php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Action\Type\FormActionType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder->addBatchAction('remove', FormActionType::class, [
            'confirmation' => [
                'translation_domain' => 'KreyuDataTable',
                'label_title' => 'Action confirmation',
                'label_description' => 'Are you sure you want to execute this action?',
                'label_confirm' => 'Confirm',
                'label_cancel' => 'Cancel',
                'type' => 'danger', // "danger", "warning" or "info"
            ],
        ]);
    }
}
```

For reference, see [action's `confirmation` option documentation](../../reference/actions/types/action/#confirmation).

## Conditionally rendering the action

Action visibility can be configured using its [`visible` option](../../reference/actions/types/action/#visible):

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\FormActionType;

$builder
    ->addBatchAction('remove', FormActionType::class, [
        'visible' => $this->isGranted('ROLE_ADMIN'),
    ])
;
```

Another approach would be simply not adding the action at all:

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;

if ($this->isGranted('ROLE_ADMIN')) {
    $builder->addBatchAction('remove', FormActionType::class);
}
```

What differentiates those two methods, is that by using the `visible` option, the action is still defined in the data table, but is not rendered in the view.
It may be useful in some cases, for example, when the actions can be modified outside the data table builder.

