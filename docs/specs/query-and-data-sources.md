# Query, Data Sources, Pagination & Sorting (Implementation)

**Document type:** Implementation
**Source pinned to:** commit `2ace2d9`, version `0.17`

The `ProxyQuery` abstraction is the seam between the data table and any data source. Pagination and
sorting are specified here because they are applied **through** that seam.

## 1. ProxyQuery abstraction

`ProxyQueryInterface` (`src/Query/ProxyQueryInterface.php:10-17`) is intentionally minimal:

```php
sort(SortingData $sortingData): void;
paginate(PaginationData $paginationData): void;
getResult(): ResultSetInterface;
```

Filtering is **not** here — filters apply themselves via handlers (see [filters.md](filters.md) §2).
Each data source implements this interface; the right one is chosen by the first
`ProxyQueryFactoryInterface` (`src/Query/ProxyQueryFactoryInterface.php`) whose `supports($data)`
returns true (`src/DataTableFactory.php:36-42`), in registration order (Doctrine before array, so a
`QueryBuilder` is not mistaken for an array).

| Source | ProxyQuery | Factory | `supports()` | Source |
|--------|-----------|---------|--------------|--------|
| PHP array | `ArrayProxyQuery` | `ArrayProxyQueryFactory` | `is_array($data)` | `src/Query/ArrayProxyQuery.php:11-72` |
| Doctrine ORM | `DoctrineOrmProxyQuery` | `DoctrineOrmProxyQueryFactory` | `$data instanceof QueryBuilder` | [bridges.md](bridges.md) §Doctrine |

`ArrayProxyQuery` sorts in memory with `usort` over `SortingData::getColumns()` using PropertyAccessor
and slices via `array_slice` from `PaginationData::getOffset()` (`:25-61`).

## 2. ResultSet

`ResultSetInterface` (`src/Query/ResultSetInterface.php`) extends `IteratorAggregate` + `Countable`:
`getIterator()` (current page items), `getCurrentPageItemCount()`, `getTotalItemCount()`. `ResultSet`
(`src/Query/ResultSet.php`) falls back `totalItemCount ??= currentPageItemCount`. This lets each
backend compute counts its own way (array eagerly; Doctrine via a `Paginator` COUNT — see
[bridges.md](bridges.md)).

## 3. Pagination

`PaginationData` (`src/Pagination/PaginationData.php`): `page` (default 1), `perPage` (default 25 via
`PaginationInterface::DEFAULT_PER_PAGE`); `getOffset() = perPage * (page - 1)`; `fromArray` validates
page > 0 and perPage null-or-> 0.

`DataTable::paginate` (`src/DataTable.php:511-533`): guard if disabled → `PRE_PAGINATE` →
`query->paginate` → update `originalQuery` → persist (if enabled & `persistence:true`) → reset cached
pagination/result set → `POST_PAGINATE`.

`PaginationInterface` (`src/Pagination/Pagination.php`): `getPageCount` (ceil division),
`has{Previous,Next}Page`, windowed `get{First,Last}VisiblePageNumber` (`SIDE_PAGE_LIMIT = 3`),
1-indexed item boundaries. The constructor throws `CurrentPageOutOfRangeException` when the page is
out of range with a non-empty result; `DataTable::createPagination` catches it and resets to page 1
(`src/DataTable.php:687-695`). `PaginationView` flattens this for templates.

Request: `HttpFoundationRequestHandler::paginate` skips entirely when no page parameter is present
(`:77-98`).

### 3a. Pagination factory & adjustable page window (#219)

> **Status: Implemented** ([issue #219](https://github.com/Kreyu/data-table-bundle/issues/219)). §3a
> describes the implemented design; line citations are against the working tree. Keyset (cursor)
> pagination remains a separate proposal on the same `paginate(PaginationData)` seam — see
> [pagination-keyset.md](pagination-keyset.md) (#175), which consumes the factory seam defined here.
>
> **BC note:** adding `getPaginationFactory`/`setPaginationFactory` to `DataTableConfigInterface` and
> `DataTableConfigBuilderInterface` breaks any *external* class that implements those interfaces directly
> (the bundle's own `DataTableConfigBuilder` is updated). Release-note this for the next minor.

**Problem (verified).** `Pagination` is instantiated in exactly one place — `new Pagination(...)` at
`src/DataTable.php:681` — with no factory seam, and `SIDE_PAGE_LIMIT = 3` is a `public const`
(`src/Pagination/Pagination.php:9`) used only by the view-window methods (`:64-74`). A user cannot adjust
the window or substitute their own `Pagination` without forking `createPagination`.

**Design.** New types under `src/Pagination/`:

```php
final class PaginationContext { // factory's single named parameter object (DTO)
    public function __construct(
        public readonly int $currentPageNumber,
        public readonly int $currentPageItemCount,
        public readonly int $totalItemCount,
        public readonly ?int $itemNumberPerPage,
        public readonly int $visiblePagesRange,
    ) {}
}
interface PaginationFactoryInterface { public function create(PaginationContext $context): PaginationInterface; }
final class PaginationFactory implements PaginationFactoryInterface { /* returns new Pagination(...) */ }
```

- `PaginationFactory::create` must let `Pagination`'s constructor `CurrentPageOutOfRangeException`
  **propagate** — `DataTable::createPagination` depends on the throw to reset to page 1 (`:687-695`).
- `Pagination::__construct` gains one **optional, trailing** param `int $visiblePagesRange = self::SIDE_PAGE_LIMIT`;
  the two window methods read `$this->visiblePagesRange`. The const is retained, so all existing call
  sites and constant-readers are unaffected (BC-safe for construction + the constant; a subclass that
  overrides the window methods is unaffected unless it opts to read the new property). `PaginationInterface`
  is untouched — the range stays a view-window detail.
- `DataTable::createPagination` (`:676-696`) delegates:
  `$factory = $this->config->getPaginationFactory() ?? new PaginationFactory();` then
  `$factory->create(new PaginationContext(..., visiblePagesRange: $this->config->getOption('page_visible_range', 3)))`,
  keeping the existing `CurrentPageOutOfRangeException` catch/reset loop unchanged.
- The factory is an injected, overridable service → it gets a config member + `get/setPaginationFactory`
  on `DataTableConfigInterface`/`DataTableConfigBuilder`, modelled on the **nested** feature-factory
  members `filterFactory`/`exporterFactory` (setter throws if `locked`). `page_visible_range` is a plain
  resolved option read via `getOption` (`src/DataTableConfigInterface.php:42`), mirroring `per_page_choices`
  (`src/Type/DataTableType.php:176`) — no builder setter or config getter; default `3` = current behavior.

**Wiring** (factory mirrors nested `filter_factory`/`exporter_factory`; `page_visible_range` mirrors
`per_page_choices`):

| # | File / location | Change |
|---|-----------------|--------|
| 1 | `src/Type/DataTableType.php` `configureOptions` (`:160-200`) | Option `pagination_factory` default `$this->defaults['pagination']['pagination_factory'] ?? null`, `setAllowedTypes(['null', PaginationFactoryInterface::class])`. Option `page_visible_range` default `$this->defaults['pagination']['page_visible_range'] ?? 3`, `setAllowedTypes('int')`, `setAllowedValues(fn (int $v) => $v >= 0)`. |
| 2 | `src/Type/DataTableType.php` `buildDataTable` `$setters` (`:43-71`) | Add **only** `'pagination_factory' => $builder->setPaginationFactory(...)`. Do **not** add `page_visible_range` (read at use-site, like `per_page_choices`). |
| 3 | `src/DependencyInjection/Configuration.php` inside `arrayNode('pagination')` (`:74-94`) | `->scalarNode('pagination_factory')->defaultValue('kreyu_data_table.pagination.factory')->end()` and `->integerNode('page_visible_range')->defaultValue(3)->min(0)->end()`. |
| 4 | `src/DependencyInjection/KreyuDataTableExtension.php` `$serviceReferenceNodes` (`:111-120`) | Add `'pagination_factory'` so the config string becomes a `Reference`. |
| 5 | `src/Resources/config/pagination.php` (beside the existing `url_generator` block) | Register `kreyu_data_table.pagination.factory` → `PaginationFactory::class`; alias `PaginationFactoryInterface`. Not `core.php` (which holds no pagination services). |

No Twig change: `PaginationView` already exposes `first/last_visible_page_number` (`:27-28`), now reflecting
the configured range.

**End-user surface (after the change):** per-table window via the `page_visible_range` option (or globally
via `kreyu_data_table.defaults.pagination.page_visible_range`); decorate `PaginationFactoryInterface`
(`#[AsDecorator('kreyu_data_table.pagination.factory')]`); or replace `Pagination` wholesale via a custom
factory service set on the `pagination_factory` option / global default.

**Window algorithm (verified, asymmetric — NOT `current ± range`).** From `Pagination.php:64-74`:
`leftSideAddition = max(range − (pageCount − current), 0)`; `first = max(current − range − leftSideAddition, 1)`;
`last = min(first + range·2, pageCount)`. Anchors (`PaginationTest.php:90-130`, 25 pages, range 3):
current=10 → 7..13; current=1 → 1..7; current=25 → 19..25.

**#219 tests to add:** `PaginationFactoryTest` (creates a configured `Pagination`; propagates
`CurrentPageOutOfRangeException`); extend `PaginationTest` with the `visiblePagesRange` param (anchors
above); extend `DataTableConfigBuilderTest` for the locked-`setPaginationFactory` guard; extend
`DataTableTest` for default-factory + `page_visible_range` end-to-end.

**#219 assumptions / open question:** the nested-feature-factory wiring (scalar node → `Reference` →
option → builder setter → config getter) is the bundle's standard for an injectable overridable service
(verified across `filter_factory`/`exporter_factory`/`request_handler` at `0ad7af1`). Final names are
fixed: option `page_visible_range`, service id `kreyu_data_table.pagination.factory`. Open (cosmetic):
whether to `@deprecate` `Pagination::SIDE_PAGE_LIMIT` — default is to keep it as the documented default.

## 4. Sorting

`SortingColumnData` (`src/Sorting/SortingColumnData.php`): `name`, `direction` (`'asc'`|`'desc'`|`'none'`,
lowercased, validated), lazy `propertyPath` (defaults to a `PropertyPath` of the name). `SortingData`
(`src/Sorting/SortingData.php`) is an ordered map supporting multi-column sort; `fromArray` accepts
column-data objects, nested arrays, or `name => direction` shorthand.

`DataTable::sort` (`src/DataTable.php:535-562`): guard → `PRE_SORT` →
`removeRedundantColumns` (drops columns not present or not `isSortable`) →
`ensureValidPropertyPaths` (canonicalizes each to the column's configured sort path) →
`query->sort` → persist → **reset pagination to page 1** → `POST_SORT`. This validation-at-application
sanitizes request/persisted sort data before it reaches the query.

> **Note (verified):** `src/Sorting/Direction.php` and `src/Sorting/SortDirection.php` define enums
> that are **not used** anywhere — direction is handled as a string throughout (incl.
> `ColumnSortUrlGenerator`, which matches on `'asc'`/`'desc'`). They appear vestigial. Removing them
> is a candidate cleanup, tracked as an Open Question below rather than asserted as intent.

## 5. State sourcing
On `initialize()` each of pagination/sorting reads persisted state (if enabled), else the config
default, else an empty value, and is applied with `persistence:false` (see
[core-data-table.md](core-data-table.md) §1, [persistence.md](persistence.md) §4).

## Anti-Patterns (DO NOT)
| Don't | Do instead | Why |
|-------|-----------|-----|
| Add filter methods to `ProxyQueryInterface` | Use filter handlers | Keeps the seam minimal (`src/Query/ProxyQueryInterface.php`) |
| Trust request sort data as-is | Let `sort()` validate it | `removeRedundantColumns`/`ensureValidPropertyPaths` sanitize (`src/DataTable.php:547-548`) |
| Hand-roll `Direction`/`SortDirection` enum usage | Use `'asc'`/`'desc'`/`'none'` strings | The enums are unused; strings are the contract (`src/Sorting/SortingColumnData.php:35`) |
| Assume a page is always paginated | Page param may be absent → skipped | `HttpFoundationRequestHandler::paginate` returns early (`:93-95`) |
| *(§3a #219)* `new Pagination(...)` directly | Go through `PaginationFactoryInterface::create` | The factory is the substitution seam (`src/DataTable.php:681`) |
| *(§3a #219)* Make the factory catch `CurrentPageOutOfRangeException` | Let it propagate | `createPagination` resets to page 1 on the throw (`:687-695`) |
| *(§3a #219)* Add a required param to `Pagination::__construct` | Add it optional + trailing with the const default | Keeps direct construction BC-safe |
| *(§3a #219)* Wire `page_visible_range` as a service/config member | Read it as a plain option via `getOption` (like `per_page_choices`) | It is render-time data, not an injected service |
| *(§3a #219)* Default the new config nodes to anything but current behavior | `page_visible_range = 3`, factory = shipped default service | Any other default silently changes every existing table |

## Error Handling Matrix
| Condition | Detection | Response | Exception | Source |
|-----------|-----------|----------|-----------|--------|
| Page out of range (non-empty) | constructor check | caught → reset page 1 | `CurrentPageOutOfRangeException` | `src/Pagination/Pagination.php`, caught `src/DataTable.php:687-695` |
| Non-`ColumnInterface` in sort validation | `instanceof` | abort | `UnexpectedTypeException` | `src/Sorting/SortingData.php:55,83` |
| Invalid page/perPage in `fromArray` | OptionsResolver | abort | validation exception | `src/Pagination/PaginationData.php:32-37` |
| Invalid sort direction | OptionsResolver `allowedValues` | abort | validation exception | `src/Sorting/SortingColumnData.php:35` |
| Non-factory tagged as proxy factory | `instanceof` | abort | `UnexpectedTypeException` | `src/DataTableRegistry.php:132-133` |
| *(§3a #219)* `pagination_factory` neither null nor `PaginationFactoryInterface` | `OptionsResolver` allowed types | abort at build | options validation exception | §3a wiring row 1 |
| *(§3a #219)* `page_visible_range` not int / negative | `OptionsResolver` allowed types + value | abort at build | options validation exception | §3a wiring row 1 |
| *(§3a #219)* `pagination_factory` option user-set to null | `buildDataTable` skips setter (`src/Type/DataTableType.php:73-77`) | fall back to `new PaginationFactory()` | (handled) | §3a design |

## Invariants & Existing Tests
| Invariant | Test |
|-----------|------|
| Array proxy query sort/paginate/result | `tests/Unit/Query/{ArrayProxyQueryTest,ArrayProxyQueryFactoryTest,ResultSetTest}.php` |
| Pagination math & out-of-range reset | `tests/Unit/Pagination/PaginationTest.php`, `PaginationUrlGeneratorTest.php` |
| Doctrine proxy query / result set / paginator | `tests/Unit/Bridge/Doctrine/Orm/Query/*`, `Paginator/PaginatorFactoryTest.php` |
| Custom proxy factory selection | `tests/Fixtures/DataTable/Query/*` + `tests/Unit/DataTableFactoryTest.php` |
| *(§3a #219)* Factory creates configured `Pagination` from a context; propagates out-of-range; window-range param incl. edge/zero; BC default | `tests/Unit/Pagination/PaginationFactoryTest.php`, `tests/Unit/Pagination/PaginationTest.php` |

## Open Questions
| Question | Why it matters | Blocks |
|----------|----------------|--------|
| Should the unused `Direction`/`SortDirection` enums be removed or wired in? | Dead code vs. intended future API; affects public surface | Nothing — descriptive only; a maintainer decision |

## References
| Topic | Location | Anchor |
|-------|----------|--------|
| Filters apply through handlers, not the query | [filters.md](filters.md) | §2 |
| Doctrine proxy query internals | [bridges.md](bridges.md) | §Doctrine |
| Persistence of pagination/sorting | [persistence.md](persistence.md) | §4 |
| ProxyQuery interface | `src/Query/ProxyQueryInterface.php` | `:10-17` |
