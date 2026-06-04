# Pagination

The data tables can be _paginated_, which is crucial when working with large data sources.

[[toc]]

## Toggling the feature

By default, the pagination feature is **enabled** for every data table.
This can be configured thanks to the `pagination_enabled` option:

::: code-group
```yaml [Globally (YAML)]
kreyu_data_table:
  defaults:
    pagination:
      enabled: true
```

```php [Globally (PHP)]
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $defaults = $config->defaults();
    $defaults->pagination()->enabled(true);
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
            'pagination_enabled' => true,
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
                'pagination_enabled' => true,
            ],
        );
    }
}
```
:::

::: tip If you don't see the pagination controls, make sure your data table has enough records!
By default, every page contains 25 records.
Built-in themes display pagination controls only when the data table contains more than one page.
Also, remember that you can [change the default pagination data](#default-pagination), reducing the per-page limit.
:::

::: tip Pagination is enabled, but changing the page does nothing?
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

## Saving applied pagination

By default, the pagination feature [persistence](persistence.md) is **disabled** for every data table.

You can configure the [persistence](persistence.md) globally using the package configuration file, or its related options:

::: code-group
```yaml [Globally (YAML)]
kreyu_data_table:
  defaults:
    pagination:
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
    $defaults->pagination()
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
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDataTableType extends AbstractDataTableType
{
    public function __construct(
        private PersistenceAdapterInterface $persistenceAdapter,
        private PersistenceSubjectProviderInterface $persistenceSubjectProvider,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'pagination_persistence_enabled' => true,
            'pagination_persistence_adapter' => $this->persistenceAdapter,
            'pagination_persistence_subject_provider' => $this->persistenceSubjectProvider,
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

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    
    public function __construct(
        private PersistenceAdapterInterface $persistenceAdapter,
        private PersistenceSubjectProviderInterface $persistenceSubjectProvider,
    ) {
    }
    
    public function index()
    {
        $dataTable = $this->createDataTable(
            type: ProductDataTableType::class, 
            query: $query,
            options: [
                'pagination_persistence_enabled' => true,
                'pagination_persistence_adapter' => $this->persistenceAdapter,
                'pagination_persistence_subject_provider' => $this->persistenceSubjectProvider,
            ],
        );
    }
}
```
:::

### Adding pagination loaded from persistence to URL

By default, the pagination loaded from the persistence is not visible in the URL.

It is recommended to make sure the **state** controller is enabled in your `assets/controllers.json`,
which will automatically append the pagination parameters to the URL, even if multiple data tables are visible on the same page.

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

## Default pagination

The default pagination data can be overridden using the data table builder's `setDefaultPaginationData()` method:

```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder->setDefaultPaginationData(new PaginationData(
            page: 1, 
            perPage: 25,
        ));
        
        // or by creating the pagination data from an array:
        $builder->setDefaultPaginationData(PaginationData::fromArray([
            'page' => 1, 
            'perPage' => 25,
        ]));
    }
}
```

## Configuring items per page

The per-page limit choices can be configured using the `per_page_choices` option.
Those choices will be rendered inside a select field, next to the pagination controls.

::: code-group
```yaml [Globally (YAML)]
kreyu_data_table:
  defaults:
    pagination:
      per_page_choices: [10, 25, 50, 100]
```

```php [Globally (PHP)]
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $defaults = $config->defaults();
    $defaults->pagination()
        ->perPageChoices([10, 25, 50, 100)
    ;
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
            'per_page_choices' => [10, 25, 50, 100],
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
                'per_page_choices' => [10, 25, 50, 100],
            ],
        );
    }
}
```
:::

Setting the `per_page_choices` to an empty array will hide the per-page select field.

## Configuring the visible page range

The pagination controls show a window of page numbers around the current page.
The `page_visible_range` option controls how many page numbers are shown on **each side** of the current
page — the default of `3` shows up to 7 numbers (current ± 3), shifting to stay within range near the
first and last page.

::: code-group
```yaml [Globally (YAML)]
kreyu_data_table:
  defaults:
    pagination:
      page_visible_range: 3
```

```php [Globally (PHP)]
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $defaults = $config->defaults();
    $defaults->pagination()
        ->pageVisibleRange(3)
    ;
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
            'page_visible_range' => 3,
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
                'page_visible_range' => 3,
            ],
        );
    }
}
```
:::

::: tip
Setting `page_visible_range` to `0` shows only the current page number (the first/previous/next/last
controls remain). The value must be `0` or greater.
:::

## Replacing the pagination factory

The [`Pagination`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Pagination/Pagination.php)
object is built through the `kreyu_data_table.pagination.factory` service, which implements
[`PaginationFactoryInterface`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Pagination/PaginationFactoryInterface.php).
You can **decorate** it to tweak how pagination is created, or **replace** it entirely with your own
implementation — for example, to return a custom `Pagination` subclass.

The simplest approach is to decorate the default service:

```php
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationContext;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator('kreyu_data_table.pagination.factory')]
class CustomPaginationFactory implements PaginationFactoryInterface
{
    public function __construct(
        private PaginationFactoryInterface $inner,
    ) {
    }

    public function create(PaginationContext $context): PaginationInterface
    {
        // delegate to the default factory, or build your own PaginationInterface here
        return $this->inner->create($context);
    }
}
```

::: warning
Do not catch `CurrentPageOutOfRangeException` inside the factory — the data table relies on it being
thrown to reset an out-of-range page back to the first one.
:::

To swap the factory for a single data table type (or specific data table), set the `pagination_factory`
option to your service instead of decorating the default:

```php
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDataTableType extends AbstractDataTableType
{
    public function __construct(
        private PaginationFactoryInterface $paginationFactory,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'pagination_factory' => $this->paginationFactory,
        ]);
    }
}
```

## Events

The following events are dispatched when `paginate()` method of the [`DataTableInterface`](https://github.com/Kreyu/data-table-bundle/blob/main/src/DataTableInterface.php) is called:

::: info PRE_PAGINATE
Dispatched before the pagination data is applied to the query.
Can be used to modify the pagination data, e.g. to force specific page or a per-page limit.

**See**: [`DataTableEvents::PRE_PAGINATE`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Event/DataTableEvents.php)
:::

::: info POST_PAGINATE
Dispatched after the pagination data is applied to the query and saved if the pagination persistence is enabled.
Can be used to execute additional logic after the pagination is applied.

**See**: [`DataTableEvents::POST_PAGINATE`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Event/DataTableEvents.php)
:::

The dispatched events are instance of the [`DataTablePaginationEvent`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Event/DataTablePaginationEvent.php):

```php
use Kreyu\Bundle\DataTableBundle\Event\DataTablePaginationEvent;

class DataTablePaginationListener
{
    public function __invoke(DataTablePaginationEvent $event): void
    {
        $dataTable = $event->getDataTable();
        $paginationData = $event->getPaginationData();
        
        // for example, modify the pagination data, then save it in the event
        $event->setPaginationData($paginationData); 
    }
}
```
