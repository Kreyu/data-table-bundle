---
icon: bug
order: 2
---

# Troubleshooting

This section covers common problems and how to fix them.

## Sorting is enabled but columns are not sortable

Enabling the sorting feature for the data table does not mean that any column will be sortable by itself.
By default, columns **are not** sortable. To make a column sortable, use its `sort` option.

For more information, consider reading:

- [Basic Usage > Adding columns > Making the columns sortable](basic-usage/adding-columns.md#making-the-columns-sortable)
- [Features > Sorting > Making the columns sortable](features/sorting.md#making-the-columns-sortable)

## Data table features are refreshing the page but not working

If, for example, a data table is rendered properly, but:
- clicking on pagination,
- changing sort order,
- applying filters,
- etc.

refreshes the page but does nothing else, make sure you handled the request using the `handleRequest()` method:

```php #4,13 src/Controller/ProductController.php
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    
    public function index(Request $request)
    {
        $dataTable = $this->createDataTable(ProductDataTableType::class);
        $dataTable->handleRequest($request);
    }
}
```

For more information, consider reading:

- [Basic Usage > Rendering the table > Binding request to the data table](basic-usage/rendering-the-table.md#binding-request-to-the-data-table)
- [Features > Request handlers](features/request-handlers.md)

## The N+1 problem

When using Doctrine ORM, if your data table contains columns with data from relationship:

```php # src/DataTable/Type/ProductDataTableType.php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addColumn('category.name', TextColumnType::class)
        ;
    }
}
```

...then, remember to join and select the association to prevent N+1 queries:

```php # src/Controller/ProductController.php
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    
    public function index(ProductRepository $repository)
    {
        $query = $repository->createQueryBuilder('product')
            ->addSelect('category')
            ->leftJoin('product.category', 'category')
        ;
        
        $dataTable = $this->createDataTable(
            type: ProductDataTableType::class, 
            query: $query,
        );
    }
}
```

### The N+1 problem with unused one-to-one relations

If your entity contains a one-to-one relationship that is **not** used in the data table,
the additional queries will be generated anyway, because the [Doctrine Paginator](https://www.doctrine-project.org/projects/doctrine-orm/en/2.15/tutorials/pagination.html) is **always** loading them.
To prevent that, add a hint to force a partial load:

```php # src/DataTable/Type/ProductDataTableType.php
use Doctrine\ORM\Query;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\DoctrineOrmProxyQuery;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $query = $builder->getQuery();
        
        if ($query instanceof DoctrineOrmProxyQuery) {
            $query->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true);
        }
    }
}
```

## Persistence "cache tag contains reserved characters" error

When using the default configuration, after enabling the persistence for any feature, it may result in the error:

> Cache tag "kreyu_data_table_persistence_user\@example.com" contains reserved characters "{}()/\\@:".

By default, the bundle is using a cache as a persistence storage, and currently logged-in user as a persistence subject.
To identify which data belongs to which user, the persistence subject must return a unique identifier.
To retrieve a unique identifier of a user without additional configuration, a `UserInterface::getUserIdentifier()` method is used.
Unfortunately, in some applications, it may return something with a reserved character — in case of above error, an email address "user\@example.com".

To prevent that, implement a `PersistenceSubjectInterface` interface on the User object and manually return the **unique** identifier:

```php # src/Entity/User.php
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, PersistenceSubjectInterface
{
    private int $id;
    
    public function getDataTablePersistenceIdentifier(): string
    {
        return (string) $this->id;
    }
}
```

For more information, consider reading:

- [Features > Persistence > Built-in subject providers](features/persistence.md#built-in-subject-providers)
