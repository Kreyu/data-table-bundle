# Persistence

This bundle provides a persistence feature, which is used to save data between requests.
For example, it can be used to persist applied filters or pagination, per user.

[[toc]]

## Toggling the feature

Persistence can be toggled per feature with its own configuration:

- [Saving applied pagination](pagination.md#saving-applied-pagination)
- [Saving applied sorting](sorting.md#configuring-the-feature-persistence)
- [Saving applied filters](filtering.md#configuring-the-feature-persistence)
- [Saving applied personalization](personalization.md#saving-applied-personalization)

## Persistence adapters

Adapters are classes that allow writing (to) and reading (from) the persistent data source.

### Built-in cache adapter

The bundle has a built-in cache adapter, which uses the [Symfony Cache component](https://symfony.com/doc/current/components/cache.html).

It is registered as an [abstract service](https://symfony.com/doc/current/service_container/parent_services.html) in the service container:

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

The bundle adds a `kreyu_data_table.persistence.cache.default` cache pool, which uses the `cache.adapter.filesystem` adapter, with `tags` enabled.

::: tip It is recommended to use tag-aware cache adapter!
The built-in [cache persistence clearer](#persistence-clearers) requires tag-aware cache to clear persistence data.
:::

### Creating custom adapters

To create a custom adapter, create a class that implements `PersistenceAdapterInterface`.

```php
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

<div class="tip custom-block" style="padding-top: 8px;">

Recommended namespace for the column type classes is `App\DataTable\Persistence\`.

</div>

The recommended way of creating those classes is accepting a `prefix` argument in the constructor.
This prefix will be different for each feature, for example, personalization persistence will use `personalization` prefix. 

Now, register it in the container as an abstract service:

::: code-group
```yaml [YAML]
services:
  app.data_table.persistence.database:
    class: App\DataTable\Persistence\DatabasePersistenceAdapter
    abstract: true
    arguments:
      - '@doctrine.orm.entity_manager'
```

```php [PHP]
use App\DataTable\Persistence\DatabasePersistenceAdapter;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator) {
    $configurator->services()
        ->set('app.data_table.persistence.database', DatabasePersistenceAdapter::class)
            ->args([service('doctrine.orm.entity_manager')])
            ->abstract()
    ;
```
:::

Now, create as many adapters as you need, based on the abstract definition.
For example, let's create an adapter for personalization feature, using the `personalization` prefix:

::: code-group
```yaml [YAML]
services:
  app.data_table.personalization.persistence.database:
    parent: app.data_table.persistence.database
    arguments:
      $prefix: personalization
    tags:
      - { name: kreyu_data_table.proxy_query.factory }
```

```php [PHP]
use App\DataTable\Persistence\DatabasePersistenceAdapter;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator) {
    $configurator->services()
        ->set('app.data_table.personalization.persistence.database')
            ->parent('app.data_table.persistence.database')
            ->arg('$prefix', 'personalization')
    ;
```
:::

The data tables can now be configured to use the new persistence adapter for the personalization feature:

::: code-group
```yaml [Globally (YAML)]
kreyu_data_table:
  defaults:
    personalization:
      persistence_adapter: app.data_table.personalization.persistence.database
```

```php [Globally (PHP)]
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $config->defaults()
        ->personalization()
            ->persistenceAdapter('app.data_table.personalization.persistence.database')
    ;
};
```
 
```php [For data table type]
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

```php [For specific data table]
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
:::

## Persistence subjects

Persistence subject can be any object that implements `PersistenceSubjectInterface`.

The value returned in the `getDataTablePersistenceIdentifier()` is used in
[persistence adapters](#persistence-adapters) to associate persistent data with the subject.

### Subject providers

Persistence subject providers are classes that allow retrieving the [persistence subjects](#persistence-subjects).  
Those classes contain `provide` method, that should return the subject, or throw an `PersistenceSubjectNotFoundException`.

### Built-in token storage subject provider

The bundle has a built-in token storage subject provider, which uses the [Symfony Security component](https://symfony.com/doc/current/security.html) to retrieve currently logged-in user.
This provider uses the [UserInterface](https://github.com/symfony/symfony/blob/6.4/src/Symfony/Component/Security/Core/User/UserInterface.php) `getUserIdentifier()` 
method to retrieve the persistence identifier. 

::: danger The persistence identifier must be **unique** per user!
Otherwise, multiple users will override each other's data, like applied filters or current page.
:::

You can manually provide the persistence identifier by implementing the `PersistenceSubjectInterface` interface on your User entity used by the security:

```php
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

::: tip Persistence "cache tag contains reserved characters" error?
If your User entity returns email address in `getUserIdentifier()` method, this creates a conflict 
when using the [cache adapter](#built-in-cache-adapter), because the `@` character cannot be used as a cache key.

For more information, see [troubleshooting section](../troubleshooting.md#persistence-cache-tag-contains-reserved-characters-error).
:::

### Creating custom subject providers

To create a custom subject provider, create a class that implements `PersistenceSubjectProviderInterface`:

```php
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

::: code-group
```yaml [YAML]
services:
  app.data_table.persistence.subject_provider.custom:
    class: App\DataTable\Persistence\CustomPersistenceSubjectProvider
    tags:
      - { name: kreyu_data_table.persistence.subject_provider }
```

```php [PHP]
use App\DataTable\Persistence\CustomPersistenceSubjectProvider;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator) {
    $configurator->services()
        ->set('app.data_table.persistence.database', CustomPersistenceSubjectProvider::class)
            ->tag('kreyu_data_table.persistence.subject_provider')
    ;
}
```
:::

The data tables can now be configured to use the new persistence subject provider for any feature. 
For example, for personalization feature:

::: code-group
```yaml [Globally (YAML)]
kreyu_data_table:
  defaults:
    personalization:
      persistence_subject_provider: app.data_table.persistence.subject_provider.custom
```

```php [Globally (PHP)]
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $config->defaults()
        ->personalization()
            ->persistenceSubjectProvider('app.data_table.persistence.subject_provider.custom')
    ;
};
```

```php [For data table type]
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectProviderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDataTableType extends AbstractDataTableType
{
    public function __construct(
        #[Autowire(service: 'app.data_table.persistence.subject_provider.custom')]
        private PersistenceSubjectProviderInterface $persistenceSubjectProvider,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'personalization_persistence_subject_provider' => $this->persistenceSubjectProvider,
        ]);
    }
}
```

```php [For specific data table]
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
:::

## Persistence clearers

Persistence data can be cleared using persistence clearers, which are classes that implement [`PersistenceClearerInterface`](#).
Those classes contain a `clear()` method, which accepts a [persistence subject](#persistence-subjects) as an argument.

Because the bundle has a built-in cache adapter, it also provides a cache persistence clearer:

```bash
$ bin/console debug:container kreyu_data_table.persistence.clearer.cache
```

Let's assume, that the user has a "Clear data table persistence" button, somewhere on the "settings" page. 
Handling this button in controller is very straightforward:

```php
use App\Entity\User;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceClearerInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController
{
    #[Route('/users/{id}/clear-persistence')]
    public function clearPersistence(User $user, PersistenceClearerInterface $persistenceClearer)
    {
        $persistenceClearer->clear($user);
        
        // Flash with success, redirect, etc...
    }
}
```
