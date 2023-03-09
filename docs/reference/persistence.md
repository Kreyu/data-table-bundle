# Persistence

This bundle provides persistence feature, ready to use with data table sorting, pagination, filtration and personalization.

## Persistence adapters

Adapters are classes that allows writing (to) and reading (from) the persistent data source.  
By default, there's only one adapter integrating with Symfony Cache contracts.

### Using built-in cache adapter

Built-in cache adapter accepts two arguments in constructor:

- cache implementing Symfony's `Symfony\Contracts\Cache\CacheInterface`
- prefix string used to differentiate different data sets, e.g. filtration persistence uses `filtration` prefix

In service container, it is registered as an [abstract service](https://symfony.com/doc/current/service_container/parent_services.html):

```bash
bin/console debug:container kreyu_data_table.persistence.adapter.cache
```

Creating new services based on the abstract adapter can be performed in service container.

### Creating custom adapters

To create a custom adapter, create a class that implements `PersistenceAdapterInterface`:

```php
// src/DataTable/Persistence/DatabasePersistenceAdapter.php
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

...and register it in the container as an abstract service:

```yaml
services:
  app.data_table.persistence.database:
    class: App\DataTable\Persistence\DatabasePersistenceAdapter
    abstract: true
    arguments:
      - '@doctrine.orm.entity_manager'
```

## Persistence subjects

Persistence subject can be any object that implements `PersistenceSubjectInterface`.

The value returned in the `getDataTablePersistenceIdentifier()` is used in 
[persistence adapters](#persistence-adapters) to associate persistent data with the subject.

## Persistence subject providers

Persistence subject providers are classes that allows retrieving the [persistence subjects](#persistence-subjects).  
Those classes contain `provide` method, that should return the subject, or throw an `PersistenceSubjectNotFoundException`.  

By default, there's only one provider, integrating with Symfony token storage, to retrieve currently logged-in user.
The token storage persistence subject provider uses the [UserInterface getUserIdentifier() method](https://github.com/symfony/symfony/blob/6.3/src/Symfony/Component/Security/Core/User/UserInterface.php#L60)
is used as the persistence identifier. If you wish to override this behavior without modifying the `getUserIdentifier()` method, implement the `PersistenceSubjectInterface` on the User entity:

```php
// src/Entity/User.php
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

### Creating custom persistence subject providers

To create a custom subject provider, create a class that implements `PersistenceSubjectProviderInterface`:

```php
// src/DataTable/Persistence/CustomPersistenceSubjectProvider.php
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

When using default container configuration, that provider should be ready to use.  
If not, consider tagging this class as `kreyu_data_table.persistence.subject_provider`:

```yaml
services:
  app.data_table.persistence.subject_provider.custom:
    class: App\DataTable\Persistence\CustomPersistenceSubjectProvider
    tags:
      - { name: kreyu_data_table.persistence.subject_provider }
```