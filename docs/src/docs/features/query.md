# Query

A data table needs a query to fetch its rows. You can either pass it from the controller or let the data table type build a default one.

[[toc]]

## Passing the query from the controller

The classic way: the controller provides the query as the second argument of `createDataTable()`.

```php
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;

    public function index(Request $request, ProductRepository $repository): Response
    {
        $dataTable = $this->createDataTable(
            ProductDataTableType::class,
            $repository->createQueryBuilder('p'),
        );

        $dataTable->handleRequest($request);

        return $this->render('product/index.html.twig', [
            'products' => $dataTable->createView(),
        ]);
    }
}
```

Any value that a registered `ProxyQueryFactoryInterface` supports works (Doctrine `QueryBuilder`, array, etc.).

## Defining a default query in the type

When the controller does not need to customize the query, the type can build it via `createQuery()`:

```php
use App\Repository\ProductRepository;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class ProductDataTableType extends AbstractDataTableType
{
    public function __construct(
        private ProductRepository $repository,
    ) {
    }

    public function createQuery(array $options): mixed
    {
        return $this->repository->createQueryBuilder('p');
    }
}
```

The controller can then omit the second argument:

```php
$dataTable = $this->createDataTable(ProductDataTableType::class);
```

`createQuery()` is called **once per data table creation**, so returning a fresh `QueryBuilder` instance each time keeps data tables isolated from one another — mutations from filters, sorting, or pagination on one data table never leak into the next.

The method receives the resolved options, so a type can parameterize the query through its own options:

```php
public function configureOptions(OptionsResolver $resolver): void
{
    $resolver
        ->setDefault('active_only', false)
        ->setAllowedTypes('active_only', 'bool');
}

public function createQuery(array $options): mixed
{
    $qb = $this->repository->createQueryBuilder('p');

    if ($options['active_only']) {
        $qb->andWhere('p.active = true');
    }

    return $qb;
}
```

```php
$dataTable = $this->createDataTable(ProductDataTableType::class, null, [
    'active_only' => true,
]);
```

## Overriding from the controller

An explicit second argument always wins over `createQuery()`. This fully replaces what the type would have returned — the controller owns the query in this case:

```php
$dataTable = $this->createDataTable(ProductDataTableType::class, $customQueryBuilder);
```

There is no mechanism to "grab the type's default query and add conditions on top" — the controller does not have access to the already-built `QueryBuilder` of the type. If you need to extend the default, choose one of the patterns below.

### Option 1 — Parameterize through options

When the controller should influence *how* the type builds its query, expose a type option and branch inside `createQuery()`. This is the canonical way to reuse + extend:

```php
public function configureOptions(OptionsResolver $resolver): void
{
    $resolver
        ->setDefault('active_only', false)
        ->setAllowedTypes('active_only', 'bool');
}

public function createQuery(array $options): mixed
{
    $qb = $this->repository->createQueryBuilder('p');

    if ($options['active_only']) {
        $qb->andWhere('p.active = true');
    }

    return $qb;
}
```

```php
$dataTable = $this->createDataTable(ProductDataTableType::class, null, [
    'active_only' => true,
]);
```

### Option 2 — Share a builder in the repository

When the same base query is needed in several places (type, controller, other services), expose it from the repository and reuse it:

```php
class ProductRepository extends ServiceEntityRepository
{
    public function createBaseQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.deletedAt IS NULL');
    }
}
```

```php
// In the type:
public function createQuery(array $options): mixed
{
    return $this->repository->createBaseQueryBuilder();
}
```

```php
// In the controller, reusing the same base and adding a condition:
$qb = $repository->createBaseQueryBuilder()
    ->andWhere('p.stock > 0');

$dataTable = $this->createDataTable(ProductDataTableType::class, $qb);
```

The controller ends up overriding the default (option 1 is not used), but it starts from the same base the type would have returned.

## Parent types

When a type extends another type (via `getParent()`), `createQuery()` uses the child's return value, and falls back to the parent if the child returns `null`. This mirrors how Symfony Forms resolves `empty_data` and similar defaults.
