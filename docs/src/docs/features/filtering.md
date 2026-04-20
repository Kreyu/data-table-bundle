# Filtering

The data tables can be _filtered_, with use of the [filters](#).

[[toc]]

## Toggling the feature

By default, the filtration feature is **enabled** for every data table.
This can be configured with the `filtration_enabled` option:

::: code-group
```yaml [Globally (YAML)]
kreyu_data_table:
  defaults:
    filtration:
      enabled: true
```

```php [Globally (PHP)]
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $defaults = $config->defaults();
    $defaults->filtration()->enabled(true);
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
            'filtration_enabled' => true,
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
                'filtration_enabled' => true,
            ],
        );
    }
}
```
:::

::: tip Filtering is enabled, but submitting a filtration form does nothing? 
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

## Saving applied filters

By default, the filtration feature [persistence](persistence.md) is **disabled** for every data table.

You can configure the [persistence](persistence.md) globally using the package configuration file, or its related options:

::: code-group
```yaml [Globally (YAML)]
kreyu_data_table:
  defaults:
    filtration:
      persistence_enabled: true
      # if persistence is enabled and symfony/cache is installed, null otherwise
      persistence_adapter: kreyu_data_table.filtration.persistence.adapter.cache
      # if persistence is enabled and symfony/security-bundle is installed, null otherwise
      persistence_subject_provider: kreyu_data_table.persistence.subject_provider.token_storage
```

```php [Globally (PHP)]
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $defaults = $config->defaults();
    $defaults->filtration()
        ->persistenceEnabled(true)
        // if persistence is enabled and symfony/cache is installed, null otherwise
        ->persistenceAdapter('kreyu_data_table.filtration.persistence.adapter.cache')
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
            'filtration_persistence_enabled' => true,
            'filtration_persistence_adapter' => $this->persistenceAdapter,
            'filtration_persistence_subject_provider' => $this->persistenceSubjectProvider,
        ]);
    }
}
```

```php [For specific data table]
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceAdapterInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
                'filtration_persistence_enabled' => true,
                'filtration_persistence_adapter' => $this->persistenceAdapter,
                'filtration_persistence_subject_provider' => $this->persistenceSubjectProvider,
            ],
        );
    }
}
```
:::

### Adding filters loaded from persistence to URL

By default, the filters loaded from the persistence are not visible in the URL.

It is recommended to make sure the **state** controller is enabled in your `assets/controllers.json`,
which will automatically append the filters to the URL, even if multiple data tables are visible on the same page.

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

## Default filtration

The default filtration data can be overridden using the data table builder's `setDefaultFiltrationData()` method:

```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder->setDefaultFiltrationData(new FiltrationData([
            'name' => new FilterData(value: 'John', operator: Operator::Contains),
        ]));
        
        // or by creating the filtration data from an array:
        $builder->setDefaultFiltrationData(FiltrationData::fromArray([
            'name' => ['value' => 'John', 'operator' => 'contains'],
        ]));
    }
}
```

## Events

The following events are dispatched when `filter()` method of the [`DataTableInterface`](https://github.com/Kreyu/data-table-bundle/blob/main/src/DataTableInterface.php) is called:

::: info PRE_FILTER
Dispatched before the filtration data is applied to the query.
Can be used to modify the filtration data, e.g. to force application of some filters.

**See**: [`DataTableEvents::PRE_FILTER`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Event/DataTableEvents.php)
:::

::: info POST_FILTER
Dispatched after the filtration data is applied to the query and saved if the filtration persistence is enabled;
Can be used to execute additional logic after the filters are applied.

**See**: [`DataTableEvents::POST_FILTER`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Event/DataTableEvents.php)
:::

The dispatched events are instance of the [`DataTableFiltrationEvent`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Event/DataTableFiltrationEvent.php):

```php
use Kreyu\Bundle\DataTableBundle\Event\DataTableFiltrationEvent;

class DataTableFiltrationListener
{
    public function __invoke(DataTableFiltrationEvent $event): void
    {
        $dataTable = $event->getDataTable();
        $filtrationData = $event->getFiltrationData();
        
        // for example, modify the filtration data, then save it in the event
        $event->setFiltrationData($filtrationData); 
    }
}
```

### Listening for a given data table

Attach listeners in your data table type to scope them to a single table:

```php
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Event\DataTableEvents;
use Kreyu\Bundle\DataTableBundle\Event\DataTableFiltrationEvent;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(DataTableEvents::PRE_FILTER, function (DataTableFiltrationEvent $event) {
            $data = $event->getFiltrationData();
            // mutate $data as needed, then persist it back
            $event->setFiltrationData($data);
        });
    }
}
```

Alternatively, use an event subscriber and add it to the builder:

```php
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Kreyu\Bundle\DataTableBundle\Event\DataTableEvents;
use Kreyu\Bundle\DataTableBundle\Event\DataTableFiltrationEvent;

final class ProductFiltrationSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [DataTableEvents::PRE_FILTER => 'onPreFilter'];
    }

    public function onPreFilter(DataTableFiltrationEvent $event): void
    {
        $event->setFiltrationData($event->getFiltrationData());
    }
}

class ProductDataTableType extends AbstractDataTableType
{
    public function __construct(private readonly ProductFiltrationSubscriber $subscriber) {}

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder->addEventSubscriber($this->subscriber);
    }
}
```

### Listening globally (all data tables)

Use a DataTable type extension to register a listener for every table:

```php
use Kreyu\Bundle\DataTableBundle\Extension\AbstractDataTableTypeExtension;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Event\DataTableEvents;
use App\Listener\DataTableFiltrationListener; // invokable class from above

final class AppDataTableFiltrationExtension extends AbstractDataTableTypeExtension
{
    public function __construct(private readonly DataTableFiltrationListener $listener) {}

    public static function getExtendedTypes(): iterable
    {
        return [DataTableType::class];
    }

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(DataTableEvents::PRE_FILTER, [$this->listener, '__invoke']);
    }
}
```

Or wire a subscriber globally via the type extension:

```php
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Kreyu\Bundle\DataTableBundle\Event\DataTableEvents;
use Kreyu\Bundle\DataTableBundle\Event\DataTableFiltrationEvent;

final class GlobalFiltrationSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [DataTableEvents::PRE_FILTER => 'onPreFilter'];
    }

    public function onPreFilter(DataTableFiltrationEvent $event): void
    {
        // global filtration logic
    }
}

final class AppDataTableFiltrationExtension extends AbstractDataTableTypeExtension
{
    public function __construct(private readonly GlobalFiltrationSubscriber $subscriber) {}

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
  App\DataTable\AppDataTableFiltrationExtension:
    autowire: true
    autoconfigure: true
```

::: warning
Data table events are dispatched on a per-table dispatcher. Using `#[AsEventListener]` on a service that listens on Symfony’s global dispatcher will not receive these events — add listeners via the builder or a type extension as shown above.
:::
