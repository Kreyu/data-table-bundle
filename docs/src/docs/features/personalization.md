# Personalization

The data tables can be _personalized_, which can be helpful when working with many columns, by giving the user ability to:

- set the priority (order) of the columns;
- show or hide specific columns;

::: details Screenshots
![Personalization modal with Tabler theme](/personalization_modal.png)
:::

[[toc]]

## Prerequisites

To begin with, make sure the [Symfony UX integration is enabled](../installation.md#enable-the-symfony-ux-integration).
Then, enable the **personalization** controller in your `assets/controllers.json` file:

```json
{
    "controllers": {
        "@kreyu/data-table-bundle": {
            "personalization": {
                "enabled": true
            }
        }
    }
}
```

## Toggling the feature

By default, the personalization feature is **disabled** for every data table.

You can change this setting globally using the package configuration file, or use `personalization_enabled` option:

::: code-group
```yaml [Globally (YAML)]
kreyu_data_table:
  defaults:
    personalization:
      enabled: true
```

```php [Globally (PHP)]
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $defaults = $config->defaults();
    $defaults->personalization()->enabled(true);
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
            'personalization_enabled' => true,
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
                'personalization_enabled' => true,
            ],
        );
    }
}
```
:::

## Saving applied personalization

By default, the personalization feature [persistence](persistence.md) is **disabled** for every data table.

You can configure the [persistence](persistence.md) globally using the package configuration file, or its related options:

::: code-group
```yaml [Globally (YAML)]
kreyu_data_table:
  defaults:
    personalization:
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
    $defaults->personalization()
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
            'personalization_persistence_enabled' => true,
            'personalization_persistence_adapter' => $this->persistenceAdapter,
            'personalization_persistence_subject_provider' => $this->persistenceSubjectProvider,
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
                'personalization_persistence_enabled' => true,
                'personalization_persistence_adapter' => $this->persistenceAdapter,
                'personalization_persistence_subject_provider' => $this->persistenceSubjectProvider,
            ],
        );
    }
}
```
:::

## Default personalization

There are two ways to configure the default personalization data for the data table:

- using the columns [`priority`](../../reference/types/column/column.md#priority), [`visible`](../../reference/types/column/column.md#visible) and [`personalizable`](../../reference/types/column/column.md#personalizable) options (recommended);
- using the data table builder's `setDefaultPersonalizationData()` method;

```php src/DataTable/Type/ProductDataTableType.php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationColumnData;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        // using the columns options:
        $builder
            ->addColumn('id', NumberColumnType::class, [
                'priority' => -1,
            ])
            ->addColumn('name', TextColumnType::class, [
                'visible' => false,
            ])
            ->addColumn('createdAt', DateTimeColumnType::class, [
                'personalizable' => false,
            ])
        ;
        
        // or using the data table builder's method:
        $builder->setDefaultPersonalizationData(new PersonalizationData([
            new PersonalizationColumnData(name: 'id', priority: -1),
            new PersonalizationColumnData(name: 'name', visible: false),
        ]));
        
        // or by creating the personalization data from an array:
        $builder->setDefaultPersonalizationData(PersonalizationData::fromArray([
            // each entry default values: name = from key, priority = 0, visible = false
            'id' => ['priority' => -1],
            'name' => ['visible' => false],
        ]));
    }
}
```

## Events

The following events are dispatched when `personalize()` method of the [`DataTableInterface`](https://github.com/Kreyu/data-table-bundle/blob/main/src/DataTableInterface.php) is called:

::: info PRE_PERSONALIZE
Dispatched before the personalization data is applied to the data table.
Can be used to modify the personalization data, e.g. to dynamically specify priority or visibility of the columns.

**See**: [`DataTableEvents::PRE_PERSONALIZE`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Event/DataTableEvents.php)
:::

::: info POST_PERSONALIZE
Dispatched after the personalization data is applied to the data table and saved if the personalization persistence is enabled.
Can be used to execute additional logic after the personalization is applied.

**See**: [`DataTableEvents::POST_PERSONALIZE`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Event/DataTableEvents.php)
:::

The dispatched events are instance of the [`DataTablePersonalizationEvent`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Event/DataTablePersonalizationEvent.php):

```php
use Kreyu\Bundle\DataTableBundle\Event\DataTablePersonalizationEvent;

class DataTablePersonalizationListener
{
    public function __invoke(DataTablePersonalizationEvent $event): void
    {
        $dataTable = $event->getDataTable();
        $personalizationData = $event->getPersonalizationData();
        
        // for example, modify the personalization data, then save it in the event
        $event->setPersonalizationData($personalizationData); 
    }
}
```