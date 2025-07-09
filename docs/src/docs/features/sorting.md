# Sorting

The data tables can be _sorted_ by any defined [column](../components/columns.md). 

[[toc]]

## Toggling the feature

By default, the sorting feature is **enabled** for every data table.

You can change this setting globally using the package configuration file, or use `sorting_enabled` option:

::: code-group 
```yaml [Globally (YAML)]
kreyu_data_table:
  defaults:
    sorting:
      enabled: true
```

```php [Globally (PHP)]
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $defaults = $config->defaults();
    $defaults->sorting()->enabled(true);
};
```

```php [For data table type]
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDataTableType extends AbstractDataTableType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'sorting_enabled' => true,
        ]);
    }
}
```

```php [For specific data table]
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    
    public function index()
    {
        $dataTable = $this->createDataTable(
            type: ProductDataTableType::class, 
            query: $query,
            options: [
                'sorting_enabled' => true,
            ],
        );
    }
}
```
:::

::: tip Enabling the feature does not mean that any column will be sortable by itself.
By default, columns **are not** sortable.
:::

::: tip Sorting is enabled, but sorting does nothing?
Ensure that the `handleRequest()` method of the data table is called:

```php
class ProductController
{
    public function index(Request $request)
    {
        $dataTable = $this->createDataTable(...);
        $dataTable->handleRequest($request); // [!code ++]
    }
}
```
:::

## Making the columns sortable

To make any column sortable, use its `sort` option:

```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addColumn('id', NumberColumnType::class, [
                'sort' => true,
            ])
        ;
    }
}
```

The bundle will use the column name as the path to perform sorting on.
However, if the path is different from the column name (for example, to display "category", but sort by the "category name"), provide it using the same `sort` option:

```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addColumn('category', TextColumnType::class, [
                'sort' => 'category.name',
            ])
        ;
    }
}
```

If the column should be sorted by multiple database columns (for example, to sort by amount and currency at the same time),
when using the Doctrine ORM, provide a DQL expression as a sort property path:

```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addColumn('amount', TextColumnType::class, [
                'sort' => 'CONCAT(product.amount, product.currency)',
            ])
        ;
    }
}
```

Note that you have to specify the table alias for each field, as otherwise the data table will add the alias to the beginning, breaking the DQL parser.

Depending on the database collation, sorting can be case sensitive. You can use the DQL `LOWER` function:

```php
'sort' => 'LOWER(product.name)',
```

## Saving applied sorting

By default, the sorting feature [persistence](persistence.md) is **disabled** for every data table.

You can configure the [persistence](persistence.md) globally using the package configuration file, or its related options:

::: code-group
```yaml [Globally (YAML)]
kreyu_data_table:
  defaults:
    sorting:
      persistence_enabled: true
      # if persistence is enabled and symfony/cache is installed, null otherwise
      persistence_adapter: kreyu_data_table.sorting.persistence.adapter.cache
      # if persistence is enabled and symfony/security-bundle is installed, null otherwise
      persistence_subject_provider: kreyu_data_table.persistence.subject_provider.token_storage
```

```php [Globally (PHP)]
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $defaults = $config->defaults();
    $defaults->sorting()
        ->persistenceEnabled(true)
        // if persistence is enabled and symfony/cache is installed, null otherwise
        ->persistenceAdapter('kreyu_data_table.sorting.persistence.adapter.cache')
        // if persistence is enabled and symfony/security-bundle is installed, null otherwise
        ->persistenceSubjectProvider('kreyu_data_table.persistence.subject_provider.token_storage')
    ;
};
```

```php [For data table type]
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceAdapterInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectProviderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDataTableType extends AbstractDataTableType
{
    public function __construct(
        #[Autowire(service: 'kreyu_data_table.filtration.persistence.adapter.cache')]
        private PersistenceAdapterInterface $persistenceAdapter,
        #[Autowire(service: 'kreyu_data_table.persistence.subject_provider.token_storage')]
        private PersistenceSubjectProviderInterface $persistenceSubjectProvider,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'sorting_persistence_enabled' => true,
            'sorting_persistence_adapter' => $this->persistenceAdapter,
            'sorting_persistence_subject_provider' => $this->persistenceSubjectProvider,
        ]);
    }
}
```

```php [For specific data table]
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceAdapterInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    
    public function __construct(
        #[Autowire(service: 'kreyu_data_table.filtration.persistence.adapter.cache')]
        private PersistenceAdapterInterface $persistenceAdapter,
        #[Autowire(service: 'kreyu_data_table.persistence.subject_provider.token_storage')]
        private PersistenceSubjectProviderInterface $persistenceSubjectProvider,
    ) {
    }
    
    public function index()
    {
        $dataTable = $this->createDataTable(
            type: ProductDataTableType::class, 
            query: $query,
            options: [
                'sorting_persistence_enabled' => true,
                'sorting_persistence_adapter' => $this->persistenceAdapter,
                'sorting_persistence_subject_provider' => $this->persistenceSubjectProvider,
            ],
        );
    }
}
```
:::

### Adding sorting loaded from persistence to URL

By default, the sorting loaded from the persistence is not visible in the URL.

It is recommended to make sure the **state** controller is enabled in your `assets/controllers.json`,
which will automatically append the sorting parameters to the URL, even if multiple data tables are visible on the same page.

```json
{
    "controllers": {
        "@kreyu/data-table-bundle": {
            "state": {
                "enabled": true
            }
        }
    }
}
```

## Clearing the sorting

By default, clicking on the header of the sortable column will cycle through three states:

- ascending
- descending
- none

In some cases, you may want to disable the third "none" state, disabling the ability to clear the sorting,
and only allow cycling between ascending and descending. To achieve this, you set the `sorting_clearable`
or its global default configuration option to `false`:

::: code-group
```yaml [Globally (YAML)]
kreyu_data_table:
  defaults:
    sorting:
      clearable: false
```

```php [Globally (PHP)]
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $defaults = $config->defaults();
    $defaults->sorting()->clearable(false);
};
```

```php [For data table type]
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDataTableType extends AbstractDataTableType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'sorting_clearable' => false,
        ]);
    }
}
```

```php [For specific data table]
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;

    public function index()
    {
        $dataTable = $this->createDataTable(
            type: ProductDataTableType::class, 
            query: $query,
            options: [
                'sorting_clearable' => false,
            ],
        );
    }
}
```
:::

By default, this option is set to `true`.

## Default sorting

The default sorting data can be overridden using the data table builder's `setDefaultSortingData()` method:

```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingColumnData;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder->setDefaultSortingData(new SortingData([
            new SortingColumnData('id', 'desc'),
        ]));
        
        // or by creating the sorting data from an array:
        $builder->setDefaultSortingData(SortingData::fromArray([
            'id' => 'desc',
        ]));
    }
}
```

::: tip The initial sorting can be performed on multiple columns!
Although, with built-in themes, the user can perform sorting only by a single column.  
:::

## Events

The following events are dispatched when `sort()` method of the [`DataTableInterface`](https://github.com/Kreyu/data-table-bundle/blob/main/src/DataTableInterface.php) is called:

::: info PRE_SORT
Dispatched before the sorting data is applied to the query.
Can be used to modify the sorting data, e.g. to force sorting by additional column.

**See**: [`DataTableEvents::PRE_SORT`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Event/DataTableEvents.php)
:::

::: info POST_SORT
Dispatched after the sorting data is applied to the query and saved if the sorting persistence is enabled;
Can be used to execute additional logic after the sorting is applied.

**See**: [`DataTableEvents::POST_SORT`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Event/DataTableEvents.php)
:::

The dispatched events are instance of the [`DataTableSortingEvent`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Event/DataTableSortingEvent.php):

```php
use Kreyu\Bundle\DataTableBundle\Event\DataTableSortingEvent;

class DataTableSortingListener
{
    public function __invoke(DataTableSortingEvent $event): void
    {
        $dataTable = $event->getDataTable();
        $sortingData = $event->getSortingData();
        
        // for example, modify the sorting data, then save it in the event
        $event->setSortingData($sortingData); 
    }
}
```
