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

### Listening for a given data table

Attach listeners directly in your data table type using the builder. This scopes the listener to that single table:

```php
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Event\DataTableEvents;
use Kreyu\Bundle\DataTableBundle\Event\DataTablePaginationEvent;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(DataTableEvents::PRE_PAGINATE, function (DataTablePaginationEvent $event) {
            $data = $event->getPaginationData();
            // mutate $data as needed, then persist it back
            $event->setPaginationData($data);
        });
    }
}
```

Alternatively, use an event subscriber and add it to the builder:

```php
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Kreyu\Bundle\DataTableBundle\Event\DataTableEvents;
use Kreyu\Bundle\DataTableBundle\Event\DataTablePaginationEvent;

final class ProductPaginationSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            DataTableEvents::PRE_PAGINATE => 'onPrePaginate',
        ];
    }

    public function onPrePaginate(DataTablePaginationEvent $event): void
    {
        // mutate pagination data as needed
        $event->setPaginationData($event->getPaginationData());
    }
}

class ProductDataTableType extends AbstractDataTableType
{
    public function __construct(private readonly ProductPaginationSubscriber $subscriber) {}

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder->addEventSubscriber($this->subscriber);
    }
}
```

### Listening globally (all data tables)

To react for every data table, add a DataTable type extension and register a listener there:

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

```php
use Kreyu\Bundle\DataTableBundle\Extension\AbstractDataTableTypeExtension;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Event\DataTableEvents;
use App\Listener\DataTablePaginationListener;

final class AppDataTablePaginationExtension extends AbstractDataTableTypeExtension
{
    public function __construct(private readonly DataTablePaginationListener $listener) {}

    public static function getExtendedTypes(): iterable
    {
        return [DataTableType::class];
    }

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(DataTableEvents::PRE_PAGINATE, [$this->listener, '__invoke']);
    }
}
```

Or wire a subscriber globally via the type extension:

```php
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Kreyu\Bundle\DataTableBundle\Event\DataTableEvents;
use Kreyu\Bundle\DataTableBundle\Event\DataTablePaginationEvent;

final class GlobalPaginationSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [DataTableEvents::PRE_PAGINATE => 'onPrePaginate'];
    }

    public function onPrePaginate(DataTablePaginationEvent $event): void
    {
        // global logic for all data tables
    }
}

final class AppDataTablePaginationExtension extends AbstractDataTableTypeExtension
{
    public function __construct(private readonly GlobalPaginationSubscriber $subscriber) {}

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder->addEventSubscriber($this->subscriber);
    }
}
```

Register the extension as a service with autoconfiguration so it’s discovered automatically:

```yaml
# config/services.yaml
services:
  App\DataTable\AppDataTablePaginationExtension:
    autowire: true
    autoconfigure: true
```

::: warning
Data table events are dispatched on a per-table dispatcher. Using `#[AsEventListener]` on a service that listens on Symfony’s global dispatcher will not receive these events — add listeners via the builder or a type extension as shown above.
:::
