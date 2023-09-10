---
order: d
---

# Pagination

The data tables can be _paginated_, which is crucial when working with large data sources.

## Toggling the feature

By default, the pagination feature is **enabled** for every data table.
This can be configured thanks to the `pagination_enabled` option:

+++ Globally (YAML)
```yaml # config/packages/kreyu_data_table.yaml
kreyu_data_table:
  defaults:
    pagination:
      enabled: true
```
+++ Globally (PHP)
```php # config/packages/kreyu_data_table.php
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $defaults = $config->defaults();
    $defaults->pagination()->enabled(true);
};
```
+++ For data table type
```php # src/DataTable/Type/ProductDataTable.php
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
+++ For specific data table
```php # src/Controller/ProductController.php
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
+++

!!! If you don't see the pagination controls, make sure your data table has enough records!
By default, every page contains 25 records.
Built-in themes display pagination controls only when the data table contains more than one page.
Also, remember that you can [change the default pagination data](#configuring-default-pagination), reducing the per-page limit.
!!!

## Configuring the feature persistence

By default, the pagination feature [persistence](persistence.md) is **disabled** for every data table.

You can configure the [persistence](persistence.md) globally using the package configuration file, or its related options:

+++ Globally (YAML)
```yaml # config/packages/kreyu_data_table.yaml
kreyu_data_table:
  defaults:
    pagination:
      persistence_enabled: true
      # if persistence is enabled and symfony/cache is installed, null otherwise
      persistence_adapter: kreyu_data_table.sorting.persistence.adapter.cache
      # if persistence is enabled and symfony/security-bundle is installed, null otherwise
      persistence_subject_provider: kreyu_data_table.persistence.subject_provider.token_storage
```
+++ Globally (PHP)
```php # config/packages/kreyu_data_table.php
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
+++ For data table type
```php # src/DataTable/Type/ProductDataTable.php
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
            'pagination_persistence_subject' => $this->persistenceSubjectProvider->provide(),
        ]);
    }
}
```
+++ For specific data table
```php # src/Controller/ProductController.php
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
                'pagination_persistence_subject' => $this->persistenceSubjectProvider->provide(),
            ],
        );
    }
}
```
+++

## Configuring default pagination

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

## Events

The following events are dispatched when [:icon-mark-github: DataTableInterface::paginate()](https://github.com/Kreyu/data-table-bundle/blob/main/src/DataTableInterface.php) is called:

[:icon-mark-github: DataTableEvents::PRE_PAGINATE](https://github.com/Kreyu/data-table-bundle/blob/main/src/Event/DataTableEvents.php)
:   Dispatched before the pagination data is applied to the query.
    Can be used to modify the pagination data, e.g. to force specific page or a per-page limit.

[:icon-mark-github: DataTableEvents::POST_PAGINATE](https://github.com/Kreyu/data-table-bundle/blob/main/src/Event/DataTableEvents.php)
:   Dispatched after the pagination data is applied to the query and saved if the pagination persistence is enabled;
    Can be used to execute additional logic after the pagination is applied.

The listeners and subscribers will receive an instance of the [:icon-mark-github: DataTablePaginationEvent](https://github.com/Kreyu/data-table-bundle/blob/main/src/Event/DataTablePaginationEvent.php):

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