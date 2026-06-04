# Core Data Table (Implementation)

**Document type:** Implementation
**Source pinned to:** commit `2ace2d9`, version `0.17`

Specifies the runtime spine: `DataTable`, its builder/config, factory, registry, views, request
handling, lifecycle events, and parameter naming. The shared type machinery is in
[type-system.md](type-system.md) and not repeated here.

## 1. Lifecycle

| Stage | Entry | What happens | Source |
|-------|-------|--------------|--------|
| Create | `DataTableFactory::create` | wrap data into `ProxyQuery`, resolve type, create builder, `buildDataTable()` | `src/DataTableFactory.php:17-52` |
| Assemble | `DataTableBuilder::getDataTable` | auto-add batch checkbox / actions column / search filter; resolve all components; build `DataTable` with a clone of the query | `src/DataTableBuilder.php:718-779` |
| Initialize | `DataTable::initialize` | apply persisted/default pagination → sorting → filtration → personalization (all with `persistence:false`); fire `PRE/POST_INITIALIZE` | `src/DataTable.php:130-157` |
| Handle request | `DataTable::handleRequest` | delegate to the configured `RequestHandlerInterface` | `src/DataTable.php:830-837` |
| Build view | `DataTable::createView` | ensure initialized, `type->buildView()` | `src/DataTable.php:839-852` |
| Export | `DataTable::export` | clone table, optionally re-paginate to all, pick exporter, write file | `src/DataTable.php:623-655` |

`getDataTable()` requires a query — it throws `BadMethodCallException` if none was set
(`src/DataTableBuilder.php:725`). `__clone()` deep-clones config and query
(`src/DataTable.php:124-128`); the original unfiltered query is retained as `originalQuery`
(`src/DataTable.php:121`) so filtering can reset to it.

## 2. Public contract (`DataTableInterface`, `src/DataTableInterface.php:30-222`)

- **Components:** `getColumns` (priority-sorted), `getVisibleColumns`, `getHiddenColumns`,
  `getExportableColumns`, plus `get/has/add/removeColumn|Filter|Action|BatchAction|RowAction|Exporter`.
  `getX(name)` on a missing component throws `OutOfBoundsException`.
- **Operations:** `sort(SortingData)`, `filter(FiltrationData)`, `paginate(PaginationData)`,
  `personalize(PersonalizationData)`, `export(?ExportData): ExportFile`.
- **State:** `get/set{Sorting|Pagination|Filtration|Personalization|Export}Data`.
- **Data:** `getItems(): iterable`, `getPagination(): PaginationInterface`.
- **Forms:** `create{Filtration|Personalization|Export}FormBuilder(?DataTableView)`.
- **Render:** `isExporting`, `hasActiveFilters`, `handleRequest`, `createView`, `createExportView`,
  `setTurboFrameId`, `isRequestFromTurboFrame`.

`DataTableBuilderInterface` (`src/DataTableBuilderInterface.php:19-199`) extends the config builder
and adds deferred `create*/add*/remove*` for each component, the auto-add toggles, search-handler
config, and `get/setQuery` + `getDataTable`. Reserved names/priorities:

| Constant | Value | Source |
|----------|-------|--------|
| `BATCH_CHECKBOX_COLUMN_NAME` | `__batch` | `src/DataTableBuilderInterface.php:21` |
| `BATCH_CHECKBOX_COLUMN_PRIORITY` | `1000` | `:23` |
| `ACTIONS_COLUMN_NAME` | `__actions` | `:25` |
| `SEARCH_FILTER_NAME` | `__search` | `:32` |

## 3. Request handling

`HttpFoundationRequestHandler::handle` (`src/Request/HttpFoundationRequestHandler.php:25-41`) requires
a Symfony `Request` (else `UnexpectedTypeException`) and processes, **in this fixed order**:

```
filter → sort → personalize → paginate → export → turbo
```

- **filter / personalize / export** build the respective form, read the parameter from query **then**
  POST (`getRequestParameter`, `:134-145`), submit, and on valid submission call the matching data
  table operation (`:43-58`, `:100-115`, `:117-132`).
- **sort** reads the nested `[sort_<name>]` array via PropertyAccessor and applies
  `SortingData::fromArray` (`:60-75`).
- **paginate** reads page & per-page; per-page falls back to the default or
  `PaginationInterface::DEFAULT_PER_PAGE`; **if no page param is present, pagination is skipped**
  (`:77-98`).
- **turbo** sets the frame id from the `Turbo-Frame` header (`:152-155`).

### Parameter naming
Parameter names are `"{prefix}_{tableName}"` (`getParameterName`,
`src/DataTableConfigBuilder.php:870-873`). Prefixes (`src/DataTableConfigInterface.php:25-30`):
`page`, `limit` (per-page), `sort`, `filter`, `personalization`, `export`. E.g. a table named
`product` paginates on `?page_product=2&limit_product=50`.

## 4. Views

| View | Role | Source |
|------|------|--------|
| `DataTableView` | top-level vars + header row, non-personalized header row, lazy value rows, pagination, filters, actions | `src/DataTableView.php:11-35` |
| `HeaderRowView` | column header cells; `ArrayAccess`/`Countable`; rejects writes | `src/HeaderRowView.php:9-54` |
| `ValueRowView` | per-row value cells; carries `index`, original `data`, optional `origin` (export) | `src/ValueRowView.php:9-58` |
| `RowIterator` | wraps a generator; re-creates it on `rewind()` so rows iterate repeatedly | `src/RowIterator.php:7-48` |

`DataTableType::buildView` (`src/Type/DataTableType.php:80-141`) populates feature flags, parameter
names, current state (active filters, pagination/sorting data, per-page choices) and builds the
header row, lazy value rows (via `RowIterator`), pagination view, filter views, action & batch-action
views, and the filtration/personalization/export form views (each only when its feature is enabled).
`buildExportView` (`:144-152`) uses exportable columns only.

## 5. Events

Fired around each operation (`src/Event/DataTableEvents.php`); each event carries the data table and
the mutable operation payload, so listeners can rewrite it before it is applied.

| Event pair | Operation | Payload event |
|------------|-----------|---------------|
| `PRE/POST_INITIALIZE` | `initialize()` | `DataTableEvent` |
| `PRE/POST_PAGINATE` | `paginate()` | `DataTablePaginationEvent` |
| `PRE/POST_SORT` | `sort()` | `DataTableSortingEvent` |
| `PRE/POST_FILTER` | `filter()` | `DataTableFiltrationEvent` |
| `PRE/POST_PERSONALIZE` | `personalize()` | `DataTablePersonalizationEvent` |
| `PRE_EXPORT` (no POST) | `export()` | `DataTableExportEvent` |

`sort()` and `filter()` reset pagination to page 1; `filter()` first restores `originalQuery` so
filters never stack across requests (`src/DataTable.php:535-597`).

## 6. Controller helpers
- `DataTableFactoryAwareTrait` (`src/DataTableFactoryAwareTrait.php`): `createDataTable`,
  `createNamedDataTable`, `createDataTableBuilder`, `createNamedDataTableBuilder`; injects the
  factory via `#[Required]` and throws `LogicException` if absent (`:28`).
- `DataTableTurboResponseTrait`: `createDataTableTurboResponse` for Turbo Stream responses.

## Assumptions
| Assumption | If wrong, then… |
|------------|-----------------|
| `HttpFoundationRequestHandler` is the only request handler shipped. Verified at pinned commit. | A second handler would need its own ordering documented. |

## Anti-Patterns (DO NOT)
| Don't | Do instead | Why |
|-------|-----------|-----|
| Call `getDataTable()` without setting a query | Pass data to the factory or `setQuery()` | Throws `BadMethodCallException` (`src/DataTableBuilder.php:725`) |
| Call `getPagination()` / feature methods when the feature is disabled | Check `isXEnabled()` or enable it | Each throws `RuntimeException` (`src/DataTable.php:664-666`, `762-798`) |
| Mutate `HeaderRowView`/`ValueRowView` via `[]=` | Treat views as read-only | `offsetSet`/`offsetUnset` throw `BadMethodCallException` (`src/HeaderRowView.php:40-48`) |
| Re-apply filters onto an already-filtered query | Let `filter()` reset to `originalQuery` | Prevents filter stacking (`src/DataTable.php:570`) |
| Re-write persisted state during init | Pass `persistence:false` when applying initial data | `initialize()` does exactly this (`src/DataTable.php:138-152`) |

## Error Handling Matrix
| Condition | Detection | Response | Exception | Source |
|-----------|-----------|----------|-----------|--------|
| Missing component by name | existence check | abort | `OutOfBoundsException` | `src/DataTable.php:270`+ |
| Request not a `Request` | `instanceof` | abort | `UnexpectedTypeException` | `src/Request/HttpFoundationRequestHandler.php:32` |
| No request handler configured | null check | abort | `RuntimeException` | `src/DataTable.php:833` |
| Feature disabled but used | feature flag | abort | `RuntimeException` | `src/DataTable.php:664-798` |
| Persistence enabled w/o adapter/provider | null check | abort | `RuntimeException` | `src/DataTable.php:1007,1027` |
| Page out of range | `CurrentPageOutOfRangeException` caught | reset to page 1, recompute | (handled) | `src/DataTable.php:687-695` |
| Factory not injected into controller trait | null check | abort | `LogicException` | `src/DataTableFactoryAwareTrait.php:28` |

## Invariants & Existing Tests
| Invariant | Test |
|-----------|------|
| Lifecycle, operations, view building, feature gating | `tests/Unit/DataTableTest.php` |
| Builder assembly & auto-add behavior | `tests/Unit/DataTableBuilderTest.php` |
| Config builder locking & parameter names | `tests/Unit/DataTableConfigBuilderTest.php` |
| Factory creation & proxy-query selection | `tests/Unit/DataTableFactoryTest.php` |
| Controller trait wiring | `tests/Unit/DataTableFactoryAwareTraitTest.php` |

## References
| Topic | Location | Anchor |
|-------|----------|--------|
| Shared type machinery | [type-system.md](type-system.md) | §3 |
| Query/proxy & pagination/sorting detail | [query-and-data-sources.md](query-and-data-sources.md) | §1 |
| Persistence read/write timing | [persistence.md](persistence.md) | §4 |
| Twig rendering of the view | [infrastructure.md](infrastructure.md) | §Theming |
| Request handler | `src/Request/HttpFoundationRequestHandler.php` | `:25-156` |
