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

## Error Handling Matrix
| Condition | Detection | Response | Exception | Source |
|-----------|-----------|----------|-----------|--------|
| Page out of range (non-empty) | constructor check | caught → reset page 1 | `CurrentPageOutOfRangeException` | `src/Pagination/Pagination.php`, caught `src/DataTable.php:687-695` |
| Non-`ColumnInterface` in sort validation | `instanceof` | abort | `UnexpectedTypeException` | `src/Sorting/SortingData.php:55,83` |
| Invalid page/perPage in `fromArray` | OptionsResolver | abort | validation exception | `src/Pagination/PaginationData.php:32-37` |
| Invalid sort direction | OptionsResolver `allowedValues` | abort | validation exception | `src/Sorting/SortingColumnData.php:35` |
| Non-factory tagged as proxy factory | `instanceof` | abort | `UnexpectedTypeException` | `src/DataTableRegistry.php:132-133` |

## Invariants & Existing Tests
| Invariant | Test |
|-----------|------|
| Array proxy query sort/paginate/result | `tests/Unit/Query/{ArrayProxyQueryTest,ArrayProxyQueryFactoryTest,ResultSetTest}.php` |
| Pagination math & out-of-range reset | `tests/Unit/Pagination/PaginationTest.php`, `PaginationUrlGeneratorTest.php` |
| Doctrine proxy query / result set / paginator | `tests/Unit/Bridge/Doctrine/Orm/Query/*`, `Paginator/PaginatorFactoryTest.php` |
| Custom proxy factory selection | `tests/Fixtures/DataTable/Query/*` + `tests/Unit/DataTableFactoryTest.php` |

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
