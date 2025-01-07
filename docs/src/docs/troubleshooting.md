# Troubleshooting

This section covers common problems and how to fix them.

[[toc]]

## Pagination is enabled but its controls are not visible

By default, every data table has pagination feature _enabled_.
However, if you don't see the pagination controls, make sure your data table has enough records!

With default pagination data, every page contains 25 records.
Built-in themes display pagination controls only when the data table contains more than one page.
Also, remember that you can [change the default pagination data](features/pagination.md#default-pagination), reducing the per-page limit.

For more information, consider reading:

- Features › Pagination › [Toggling the feature](features/pagination.md#toggling-the-feature)
- Features › Pagination › [Default pagination](features/pagination.md#default-pagination)

## Sorting is enabled but columns are not sortable

Enabling the sorting feature for the data table does not mean that any column will be sortable by itself.
By default, columns **are not** sortable. To make a column sortable, use its `sort` option.

For more information, consider reading:

- Features › Sorting › [Making the columns sortable](features/sorting.md#making-the-columns-sortable)

## Exporting is enabled but exported files are empty

Enabling the exporting feature for the data table does not mean that any column will be exportable by itself.
By default, columns **are not** exportable. To make a column exportable, use its `export` option.

For more information, consider reading:

- Features › Exporting › [Making the columns exportable](features/exporting.md#making-the-columns-exportable)

## Data table features are refreshing the page but not working

If, for example, a data table is rendered properly, but:
- clicking on pagination,
- changing sort order,
- applying filters,
- etc.

refreshes the page but does nothing else, make sure you handled the request using the `handleRequest()` method:

```php
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

## The N+1 problems

This section covers common issues with N+1 queries when working with Doctrine ORM.

### The N+1 problem with relation columns

When using Doctrine ORM, if your data table contains columns with data from relationship:

```php
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

```php
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

```php
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

::: warning
Forcing a partial load disables lazy loading, therefore, not specifying 
every single association used in the query's `SELECT` will end up in an error.
:::

For more information, consider reading:

- [[Stack Overflow] Doctrine2 one-to-one relation auto loads on query](https://stackoverflow.com/questions/12362901/doctrine2-one-to-one-relation-auto-loads-on-query/22253783#22253783)

## The modals are not closing properly

By default, if you submit a personalization or action confirmation modal, it will be closed,
but the backdrop is still visible. This is a classic issue with Bootstrap modals working with Turbo.

This issue is not related to the DataTableBundle itself, because other modals in the application
will work the same way, unless you're doing a full redirection or handle the modals in a different way.

To fix this, you can add following JavaScript code to automatically remove the backdrop:

```js
// assets/app.js
document.addEventListener('turbo:submit-end', () => {
    if (document.body.classList.contains('modal-open')) {
        document.querySelector('.modal-backdrop').remove();
        document.body.attributes.removeNamedItem('style');
    }
});
```

::: warning
This code will remove the `style` attribute on the `<body>` element.
You should test whether this doesn't break anything in your application.
:::

For more details, see [this issue](https://github.com/Kreyu/data-table-bundle/issues/48).

## Persistence "cache tag contains reserved characters" error

When using the default configuration, after enabling the persistence for any feature, it may result in the error:

> Cache tag "kreyu_data_table_persistence_user\@example.com" contains reserved characters "{}()/\\@:".

By default, the bundle is using a cache as a persistence storage, and currently logged-in user as a persistence subject.
To identify which data belongs to which user, the persistence subject must return a unique identifier.
To retrieve a unique identifier of a user without additional configuration, a `UserInterface::getUserIdentifier()` method is used.
Unfortunately, in some applications, it may return something with a reserved character — in case of above error, an email address "user\@example.com".

To prevent that, implement a `PersistenceSubjectInterface` interface on the User object and manually return the **unique** identifier:

```php
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

- Features › Persistence › [Subject providers](features/persistence.md#subject-providers)
