# DataTableBundle — Architecture Overview (Strategic)

**Document type:** Strategic
**Source pinned to:** commit `2ace2d9` (2026-04-12), package version `0.17`

> These specs describe **how the bundle is built internally** (architecture, contracts, data flow,
> extension points), reverse-engineered from the source with file+line citations. They are distinct
> from the user-facing usage documentation under [`docs/src/`](../src/) (VitePress site), which
> describes **how to use** the bundle. When the two disagree, the code at the pinned commit wins.

## 1. What this bundle is

`kreyu/data-table-bundle` streamlines building data tables in Symfony applications. A data table is
defined with a **type class** (exactly as Symfony forms are), then created, hydrated from a data
source, sorted/filtered/paginated/personalized, rendered with Twig, and optionally exported.

**Implementation Implication:** every user-facing concept (data table, column, filter, action,
exporter) is modeled with the *same* type-system machinery — define once as a type class, reuse
everywhere. That shared machinery is specified once in [type-system.md](type-system.md) and not
repeated per subsystem.

## 2. Why the architecture looks the way it does

The bundle is a deliberate port of the Symfony Form component's architecture to the table domain.

| Form component | This bundle | Specified in |
|----------------|-------------|--------------|
| `FormTypeInterface` / `AbstractType` | `DataTableTypeInterface` / `AbstractDataTableType` | [type-system.md](type-system.md) |
| `ResolvedFormType` | `ResolvedDataTableType` (+ Column/Filter/Action/Exporter variants) | [type-system.md](type-system.md) |
| `FormBuilder` → `Form` | `DataTableBuilder` → `DataTable` | [core-data-table.md](core-data-table.md) |
| `FormView` | `DataTableView` | [core-data-table.md](core-data-table.md) |
| `FormRegistry` / `FormFactory` | `DataTableRegistry` / `DataTableFactory` | [type-system.md](type-system.md) |

**Implementation Implication:** mutable **builder** phase → immutable **config** → runtime
**instance** → **view** is the spine of every subsystem. Builders lock themselves once read; types
resolve through a registry with circular-reference detection; extensions decorate types.

As of `0.17`, all built-in type classes are `final`; behavior is shared through `getParent()`
composition, **not** PHP inheritance (CHANGELOG.md, version 0.17).

## 3. Subsystem map

| Subsystem | Responsibility | Spec |
|-----------|----------------|------|
| Type system | Shared type/resolved-type/extension/registry/factory/builder pattern | [type-system.md](type-system.md) |
| Core data table | `DataTable` runtime, lifecycle, request handling, views, events | [core-data-table.md](core-data-table.md) |
| Columns | Header/value cell definitions, value resolution, formatting | [columns.md](columns.md) |
| Filters | Filter types, operators, filter forms, query application via handlers | [filters.md](filters.md) |
| Actions | Global / batch / row actions, confirmation modals, contexts | [actions.md](actions.md) |
| Exporters | Export pipeline, `ExportFile`, strategy | [exporters.md](exporters.md) |
| Query & data sources | `ProxyQuery` abstraction, `ResultSet`, pagination, sorting | [query-and-data-sources.md](query-and-data-sources.md) |
| Persistence | Per-subject state storage (sorting/pagination/filtration/personalization) | [persistence.md](persistence.md) |
| Personalization | Column order/visibility model and form | [personalization.md](personalization.md) |
| Bridges | Doctrine ORM, OpenSpout, PhpSpreadsheet integrations | [bridges.md](bridges.md) |
| Infrastructure | DI/config, Twig & theming, profiler, maker, events, exceptions | [infrastructure.md](infrastructure.md) |

## 4. End-to-end request lifecycle (the spine)

```
Controller
  └─ DataTableFactoryAwareTrait::createDataTable(TypeClass, data)
       └─ DataTableFactory::create → createNamedBuilder
            ├─ data wrapped into a ProxyQuery (first ProxyQueryFactory that supports() it)
            ├─ registry resolves the type (parents + extensions)
            ├─ ResolvedDataTableType::createBuilder (OptionsResolver runs)
            └─ type->buildDataTable(builder, options)   ← columns/filters/actions/exporters added
       └─ builder->getDataTable()                       ← auto-columns added, components resolved, DataTable built
  └─ dataTable->handleRequest(request)
       └─ filter → sort → personalize → paginate → export → turbo   (HttpFoundationRequestHandler)
  └─ if dataTable->isExporting(): return file(dataTable->export())
  └─ else: render(view = dataTable->createView())       ← initialize() runs, view built, Twig themes render
```

Source: `src/DataTableFactory.php:17-52`, `src/DataTableBuilder.php:718-779`,
`src/Request/HttpFoundationRequestHandler.php:25-41`, `src/DataTable.php:130-157`.
Full detail in [core-data-table.md](core-data-table.md).

## 5. Cross-cutting invariants (true in every subsystem)

1. **Builder locking.** Every `*ConfigBuilder` throws `BadMethodCallException` once turned into a
   config object; mutation after lock is a bug the code refuses to commit.
2. **Circular-type detection.** Every registry throws `LogicException` when a type's `getParent()`
   chain forms a cycle, reporting the full path.
3. **Deferred resolution.** Builders store columns/filters/actions/exporters as
   `[type, options]` pairs and resolve them only at assembly time.
4. **Immutable views.** `HeaderRowView` / `ValueRowView` reject `offsetSet`/`offsetUnset`.
5. **Feature gating.** Pagination, sorting, filtration, personalization, exporting each guard their
   public methods with `RuntimeException` when the feature is disabled.

## 6. References

### Implementation detail lives in the linked specs
| Topic | Location |
|-------|----------|
| Shared type machinery, anti-patterns, error matrix | [type-system.md](type-system.md) |
| Lifecycle, events, request handling, parameter naming | [core-data-table.md](core-data-table.md) |
| Each component subsystem | columns / filters / actions / exporters / query-and-data-sources / persistence / personalization `.md` |
| DI config tree, Twig functions, themes, profiler, maker | [infrastructure.md](infrastructure.md) |
| User-facing usage & reference | [`docs/src/`](../src/) (VitePress) |

### Open Questions
| Question | Why it matters | Blocks |
|----------|----------------|--------|
| None blocking. The specs are descriptive of existing code at the pinned commit. | — | — |

*Strategic overview only. Implementation specs live in the linked documents.*
