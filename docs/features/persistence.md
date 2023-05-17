---
order: g
---

# Persistence

This bundle provides persistence feature, ready to use with data table sorting, pagination, filtration and personalization.

## Persistence adapters

Adapters are classes that allow writing (to) and reading (from) the persistent data source.

### Built-in adapters

By default, there's only one adapter integrating the [Symfony Cache](https://symfony.com/doc/current/components/cache.html).
It accepts two arguments in the constructor:

- a cache implementing Symfony's [:icon-mark-github: CacheInterface](https://github.com/symfony/contracts/blob/main/Cache/CacheInterface.php)
- prefix string used to differentiate different data sets, e.g. filtration persistence uses `filtration` prefix

In service container, it is registered as an [abstract service](https://symfony.com/doc/current/service_container/parent_services.html):

```bash
$ bin/console debug:container kreyu_data_table.persistence.adapter.cache
```

The adapters are then created based on the abstract definition:

```bash
$ bin/console debug:container kreyu_data_table.pagination.persistence.adapter.cache
$ bin/console debug:container kreyu_data_table.sorting.persistence.adapter.cache
$ bin/console debug:container kreyu_data_table.filtration.persistence.adapter.cache
$ bin/console debug:container kreyu_data_table.personalization.persistence.adapter.cache
```

### Creating custom adapters

To create a custom adapter, create a class that implements `PersistenceAdapterInterface`:

```php # src/DataTable/Persistence/DatabasePersistenceAdapter.php
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceAdapterInterface;

class DatabasePersistenceAdapter implements PersistenceAdapterInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private string $prefix,
    ) {
    }
    
    public function read(DataTableInterface $dataTable, PersistenceSubjectInterface $subject): mixed
    {
        // ...
    }

    public function write(DataTableInterface $dataTable, PersistenceSubjectInterface $subject, mixed $data): void
    {
        // ...
    }
}
```

!!!
The recommended namespace for the persistence adapters is `App\DataTable\Persistence`.
!!!

...and register it in the container as an abstract service:

+++ YAML
```yaml # config/services.yaml
services:
  app.data_table.persistence.database:
    class: App\DataTable\Persistence\DatabasePersistenceAdapter
    abstract: true
    arguments:
      - '@doctrine.orm.entity_manager'
```
+++ PHP
```php # config/services.php
use App\DataTable\Persistence\DatabasePersistenceAdapter;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services();
    $services
        ->set('app.data_table.persistence.database', DatabasePersistenceAdapter::class)
        ->args([service('doctrine.orm.entity_manager')])
        ->abstract()
    ;
```
+++

Now, create as many adapters as you need, based on the abstract definition.
For example, let's create an adapter for sorting feature, prefixed with "sorting".

+++ YAML
```yaml # config/services.yaml
services:
  app.data_table.personalization.persistence.database:
    parent: app.data_table.persistence.database
    arguments:
      $prefix: personalization
    tags:
      - { name: kreyu_data_table.proxy_query.factory }
```
+++ PHP
```php # config/services.php
use App\DataTable\Persistence\DatabasePersistenceAdapter;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services();
    $services
        ->set('app.data_table.personalization.persistence.database')
        ->parent('app.data_table.persistence.database')
        ->arg('$prefix', 'personalization')
    ;
```
+++

The data tables can now be configured to use the new persistence adapter for the personalization feature:

+++ Globally (YAML)
```yaml # config/packages/kreyu_data_table.yaml
kreyu_data_table:
  defaults:
    personalization:
      persistence_adapter: app.data_table.personalization.persistence.database
```
+++ Globally (PHP)
```php # config/packages/kreyu_data_table.php
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $defaults = $config->defaults();
    $defaults->personalization()
        ->persistenceAdapter('app.data_table.personalization.persistence.database')
    ;
};
```
+++ For data table type
```php # src/DataTable/Type/ProductDataTable.php
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceAdapterInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDataTableType extends AbstractDataTableType
{
    public function __construct(
        #[Autowire(service: 'app.data_table.personalization.persistence.database')]
        private PersistenceAdapterInterface $persistenceAdapter,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'personalization_persistence_adapter' => $this->persistenceAdapter,
        ]);
    }
}
```
+++ For specific data table
```php # src/Controller/ProductController.php
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceAdapterInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    
    public function __construct(
        #[Autowire(service: 'app.data_table.personalization.persistence.database')]
        private PersistenceAdapterInterface $persistenceAdapter,
    ) {
    }
    
    public function index()
    {
        $dataTable = $this->createDataTable(
            type: ProductDataTableType::class, 
            query: $query,
            options: [
                'personalization_persistence_adapter' => $this->persistenceAdapter,
            ],
        );
    }
}
```
+++

## Persistence subjects

Persistence subject can be any object that implements `PersistenceSubjectInterface`.

The value returned in the `getDataTablePersistenceIdentifier()` is used in
[persistence adapters](#persistence-adapters) to associate persistent data with the subject.

## Persistence subject providers

Persistence subject providers are classes that allow retrieving the [persistence subjects](#persistence-subjects).  
Those classes contain `provide` method, that should return the subject, or throw an `PersistenceSubjectNotFoundException`.

### Built-in subject providers

By default, there's only one provider, integrating with Symfony token storage, to retrieve currently logged-in user.
The token storage persistence subject provider uses the [&nbsp;:icon-mark-github: UserInterface's](https://github.com/symfony/symfony/blob/6.3/src/Symfony/Component/Security/Core/User/UserInterface.php)&nbsp;
`getUserIdentifier()` method is used as the persistence identifier. If you wish to override this behavior without modifying the `getUserIdentifier()` method, implement the `PersistenceSubjectInterface` on the User entity:

```php # src/Entity/User.php 
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectInterface;

class User implements PersistenceSubjectInterface
{
    private Uuid $uuid;
    
    public function getDataTablePersistenceIdentifier(): string
    {
        return (string) $this->uuid;
    }
}
```

### Creating custom subject providers

To create a custom subject provider, create a class that implements `PersistenceSubjectProviderInterface`:

```php # src/DataTable/Persistence/CustomPersistenceSubjectProvider.php
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectProviderInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectInterface;

class CustomPersistenceSubjectProvider implements PersistenceSubjectProviderInterface
{
    public function provide(): PersistenceSubjectInterface
    {
        // ...
    }
}
```

Subject providers must be registered as services and tagged with the `kreyu_data_table.persistence.subject_provider` tag.
If you're using the [default services.yaml configuration](https://symfony.com/doc/current/service_container.html#service-container-services-load-example),
this is already done for you, thanks to [autoconfiguration](https://symfony.com/doc/current/service_container.html#services-autoconfigure).

When using the default container configuration, that provider should be ready to use.  
If not, consider tagging this class as `kreyu_data_table.persistence.subject_provider`:

```yaml # config/services.yaml
services:
  app.data_table.persistence.subject_provider.custom:
    class: App\DataTable\Persistence\CustomPersistenceSubjectProvider
    tags:
      - { name: kreyu_data_table.persistence.subject_provider }
```
