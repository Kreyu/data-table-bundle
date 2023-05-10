---
order: h
---

# Adding actions

What is a list of products without the ability of creating and editing the records.\
Similar to data tables, columns and filters, actions are using the [Types API](../philosophy/understanding-the-types-api.md).

## One action, two ways to use it

In this bundle, there's two kinds of actions:

* global action, displayed above the data table - e.g. "Create new user";
* row action, displayed on each row, e.g. "Show", "Edit", "Delete";

## Adding global actions to the data table

Let's assume that the application has an `app_product_create` route for creating new products.\
The user should be able to click a "Create new product" button above the products data table.

<figure><img src="../.gitbook/assets/image (4).png" alt=""><figcaption><p>Global action with the built-in Tabler theme</p></figcaption></figure>

To add global action, use the builder's `addAction()` method:

{% code title="src/DataTable/Type/ProductDataTableType.php" lineNumbers="true" %}
```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        // Columns, filters and exporters added before...
        
        $builder
            ->addAction('create', ButtonActionType::class, [
                'label' => 'Create new product',
                'href' => $this->urlGenerator->generate('app_product_create'),
                'icon_attr' => [
                    // For example, using Tabler Icons
                    // https://tabler-icons.io/
                    'class' => 'ti ti-plus', 
                ],
            ])
        ;
    }
}
```
{% endcode %}

First argument represents an action name. The second argument represents a fully qualified class name of an action type, which similarly to data table, column, filter and exporter type classes, works as a blueprint for an action - and describes how to render it.

For reference, see [built-in action types](../reference/actions/types.md).

## Adding row actions to the data table

Let's assume that the application has routes for showing product details, editing and deleting the product. Those action cannot be global - because they are bound to each row, and in fact, to a specific product (the routes require product identifier).

<figure><img src="../.gitbook/assets/image (3).png" alt=""><figcaption><p>Row actions with the built-in Tabler theme</p></figcaption></figure>

To handle this type of actions, there's a built-in action column type, which allows using the same action type classes as in their global definition.

Since there are no `addAction()` method anymore, the column type accepts

The action names are passed as the `actions` option array keys. Additionally, this type requires a `type` option, which specifies the fully qualified class name of the action type - same thing as the second argument of the builder's `addAction()` method. The column type options are passed using the `type_options` option:

{% code title="src/DataTable/Type/ProductDataTableType.php" lineNumbers="true" %}
```php
use App\Entity\Product;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ActionsColumnType;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        // Columns, filters and exporters added before...
        
        $builder
            ->addColumn('actions', ActionsColumnType::class, [
                'actions' => [
                    'update' => [
                        'type' => ButtonActionType::class,
                        'type_options' => [
                            'label' => false,
                            'href' => function (Product $product): string {
                                return $this->urlGenerator->generate('app_product_update', [
                                    'id' => $product->getId(),
                                ]);
                            },
                            'icon_attr' => [
                                // For example, using Tabler Icons
                                // https://tabler-icons.io/
                                'class' => 'ti ti-user icon',
                            ],
                        ],
                    ],
                ],
            ])
        ;
    }
}
```
{% endcode %}

There's one thing missing... a delete action! But let's say it is different, because it requires a _POST request_ to delete a product. For this case, the built-in form action type will handle it:

{% code title="src/DataTable/Type/ProductDataTableType.php" lineNumbers="true" %}
```php
use App\Entity\Product;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        // Columns, filters and exporters added before...
        
        $builder
            ->addColumn('actions', ActionsColumnType::class, [
                'actions' => [
                    'update' => [
                        // Configuration added before...
                    ],
                    'delete' => [
                        'type' => FormActionType::class,
                        'type_options' => [
                            'label' => false,
                            'method' => 'POST',
                            'action' => function (Product $product): string {
                                return $this->urlGenerator->generate('app_category_delete', [
                                    'id' => $product->getId(),
                                ]);
                            },
                            'button_attr' => [
                                'class' => 'btn btn-danger btn-icon'
                            ],
                            'icon_attr' => [
                                // For example, using Tabler Icons
                                // https://tabler-icons.io/
                                'class' => 'ti ti-user icon',
                            ],
                        ],
                    ],
                ],
            ])
        ;
    }
}
```
{% endcode %}

Now everything works fine - clicking on the delete actions sends a POST request, because the action is wrapped in a form configured to use the POST method.

## Enabling action confirmation

Clicking on the delete action immediately removes the products - in some cases it may be fine, but dangerous actions should be confirmable by the user.

<figure><img src="../.gitbook/assets/image.png" alt=""><figcaption><p>Action confirmation modal with the built-in Tabler theme</p></figcaption></figure>

By default, actions are **not** confirmable, because their `confirmation` option equals `false`. To change that, set the option to `true`:

<pre class="language-php" data-title="src/DataTable/Type/ProductDataTableType.php" data-line-numbers><code class="lang-php">use App\Entity\Product;
use Kreyu\Bundle\DataTableBundle\Action\Type\FormActionType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ActionsColumnType;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        // Columns, filters and exporters added before...
        
        $builder
            ->addColumn('actions', ActionsColumnType::class, [
                'actions' => [
                    'update' => [
                        // Configuration added before...
                    ],
                    'delete' => [
                        'type' => FormActionType::class,
                        'type_options' => [
                            // Other action type options defined before...
<strong>                            'confirmation' => true,
</strong>                        ],
                    ],
                ],
            ])
        ;
    }
}
</code></pre>

Now that the data table seems to be complete, let's focus on something really special - [a personalization](../usage/personalization.md), where the user can decide which columns are visible, or even their order!
