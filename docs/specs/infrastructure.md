# Infrastructure: DI, Twig/Theming, Profiler, Maker, Events, Exceptions (Implementation)

**Document type:** Implementation
**Source pinned to:** commit `2ace2d9`, version `0.17`

Cross-cutting wiring that supports every subsystem.

## §DI — bundle bootstrap, config tree, tagging

`KreyuDataTableBundle` registers `DefaultConfigurationPass` (`src/KreyuDataTableBundle.php:15`).
`KreyuDataTableExtension` loads service files `columns/core/actions/exporter/filtration/pagination/
personalization/twig` (+ `debug` in debug mode) and autoconfigures interface → tag
(`src/DependencyInjection/KreyuDataTableExtension.php:29-61`):

| Interface | Tag |
|-----------|-----|
| `DataTableTypeInterface` / `…TypeExtensionInterface` | `kreyu_data_table.type` / `.type_extension` |
| `ColumnTypeInterface` / ext | `kreyu_data_table.column.type` / `.type_extension` |
| `FilterTypeInterface` / ext | `kreyu_data_table.filter.type` / `.type_extension` |
| `ActionTypeInterface` / ext | `kreyu_data_table.action.type` / `.type_extension` |
| `ExporterTypeInterface` / ext | `kreyu_data_table.exporter.type` / `.type_extension` |
| `PersistenceAdapterInterface` | `kreyu_data_table.persistence.adapter` |
| `ProxyQueryFactoryInterface` | `kreyu_data_table.proxy_query.factory` |

Registries consume the matching `tagged_iterator` (`src/Resources/config/core.php:41-48`). `prepend()`
registers a tagged filesystem cache pool `kreyu_data_table.persistence.cache.default` and AssetMapper
config for the Stimulus controllers when available
(`KreyuDataTableExtension.php:81-104`). `resolveConfiguration` converts service-reference **strings**
in config into `Reference` objects (`:107-129`).

### Config tree (`src/DependencyInjection/Configuration.php`, root `kreyu_data_table`)

| Path | Default | Source |
|------|---------|--------|
| `defaults.themes` | `['@KreyuDataTable/themes/base.html.twig']` | `:41-44` |
| `defaults.column_factory` / `action_factory` / `request_handler` | the bundle services | `:45-53` |
| `defaults.sorting.{enabled, persistence_enabled, persistence_adapter, persistence_subject_provider, clearable}` | `true, false, null, null, true` | `:54-73` |
| `defaults.pagination.{enabled, persistence_*}`, `per_page_choices` | `true, false…`, `[10,25,50,100]` | `:74-94` |
| `defaults.filtration.{enabled, persistence_*, filter_factory, form_factory}` | `true, false…`, bundle filter factory, `form.factory` | `:95-117` |
| `defaults.personalization.{enabled, persistence_*, form_factory}` | **`enabled: false`**, `false…`, `form.factory` | `:118-137` |
| `defaults.exporting.{enabled, exporter_factory, form_factory}` | `true`, bundle exporter factory, `form.factory` | `:138-151` |
| `profiler.max_depth` | `3` | `:154-160` |
| root `themes` | deprecated 0.12 → use `defaults.themes` (BC-migrated) | `:30-37, 19-28` |

`DefaultConfigurationPass` (`src/DependencyInjection/DefaultConfigurationPass.php:16-34`): for each
persistence context with `persistence_enabled: true`, defaults the adapter to
`kreyu_data_table.<context>.persistence.adapter.cache` (if `CacheInterface` exists) and the subject
provider to token-storage (if `TokenStorageInterface` exists).

## §Twig & Theming

`DataTableExtension` (`src/Twig/DataTableExtension.php`) exposes rendering functions —
`data_table`, `data_table_table`, `data_table_action_bar`, `data_table_header_row`,
`data_table_value_row`, `data_table_column_{label,header,value}`, `data_table_action`,
`data_table_pagination`, `data_table_{filters,personalization,export}_form` — plus URL helpers
`data_table_{filter_clear,column_sort,pagination}_url`, and the core `data_table_theme_block`
(`:36-79`). The `{% data_table_theme %}` tag is parsed by `DataTableThemeTokenParser`/
`DataTableThemeNode`.

**Theme resolution** (`renderThemeBlock`, `:389-408`): themes are iterated in **reverse** (last theme
wins); for each, the first block matching the view's block-prefix hierarchy
([type-system.md](type-system.md) §4) is rendered; `RuntimeError` if none matches.

### Built-in themes (`src/Resources/views/themes/`)
`base`, `bootstrap_5`, `tabler`, and icon variants `icons_{webfont,ux}`,
`bootstrap_icons_{webfont,ux}`, `tabler_icons_{webfont,ux}`. The base theme defines the
`kreyu_data_table*` block structure (wrapper, action bar, table head/body, results / no-results) and
wires the Stimulus `state` and `batch` controllers.

## §Profiler (debug only)

Services in `src/Resources/config/debug.php` (kernel.debug): `DataTableDataCollector` (tagged
`data_collector`), `DataTableDataExtractor`, `DataCollectorTypeExtension`, and
`Resolved*TypeFactoryDataCollectorProxy` decorators for each resolved-type factory.
`DataCollectorListener` subscribes at **priority 255** to `post_initialize/filter/paginate/sort`
to capture state (`src/DataCollector/EventListener/DataCollectorListener.php:22-29`). The collector
clones data to `profiler.max_depth` before serialization
(`src/DataCollector/DataTableDataCollector.php:42-51`); template
`@KreyuDataTable/data_collector/template.html.twig`.

## §Maker

`MakeDataTable` (`src/Maker/MakeDataTable.php`) — `make:data-table` — generates a
`…DataTableType` class extending `AbstractDataTableType` from
`src/Resources/skeleton/DataTableType.tpl.php` (empty `buildDataTable`/`configureOptions`).

## §Events (core)
`src/Event/DataTableEvents.php` constants: `PRE/POST_INITIALIZE`, `PRE/POST_PAGINATE`,
`PRE/POST_SORT`, `PRE/POST_FILTER`, `PRE/POST_PERSONALIZE`, `PRE_EXPORT` (no POST). Payload events
(`DataTable{Sorting,Pagination,Filtration,Personalization,Export}Event`) expose a mutable payload —
see [core-data-table.md](core-data-table.md) §5.

## §Exceptions
All implement the marker `ExceptionInterface` (`src/Exception/ExceptionInterface.php`):

| Class | Extends | Triggered by |
|-------|---------|--------------|
| `InvalidArgumentException` | `\InvalidArgumentException` | unknown type names, invalid args |
| `RuntimeException` | `\RuntimeException` | feature/persistence misuse at runtime |
| `BadMethodCallException` | `\BadMethodCallException` | mutation after lock; unattached component access |
| `LogicException` | `\LogicException` | circular types, contract violations |
| `OutOfBoundsException` | `\OutOfBoundsException` | missing column/filter/action/exporter by name |
| `UnexpectedTypeException` | `\InvalidArgumentException` | type mismatches (`Expected … given`) |

## Anti-Patterns (DO NOT)
| Don't | Do instead | Why |
|-------|-----------|-----|
| Register a type manually / `new` it | Implement the interface; autoconfig tags it | DI tagging + registry drive resolution (`KreyuDataTableExtension.php:29-42`) |
| Put implementation detail in strategic docs | Keep it in these implementation specs | Single-source-of-truth ([architecture-overview.md](architecture-overview.md)) |
| Override a theme by editing the base template | Add a theme via `{% data_table_theme %}` (last wins) | Reverse-order resolution (`Twig/DataTableExtension.php:397`) |
| Use the deprecated root `themes` config node | Use `defaults.themes` | Deprecated since 0.12 (`Configuration.php:32-36`) |
| Rely on the profiler in prod | It's debug-only | Registered only when `kernel.debug` (`debug.php`) |

## Error Handling Matrix
| Condition | Detection | Response | Exception | Source |
|-----------|-----------|----------|-----------|--------|
| Theme block not found in any theme | block lookup | abort render | Twig `RuntimeError` | `src/Twig/DataTableExtension.php:407` |
| Unknown type at resolution | registry | abort | `InvalidArgumentException` | `src/DataTableRegistry.php:77` |
| Type mismatch in DI/registry | `instanceof` | abort | `UnexpectedTypeException` | `src/DataTableRegistry.php:105,118,133` |

## Invariants & Existing Tests
| Invariant | Test |
|-----------|------|
| Twig functions, theme node/token parser | `tests/Unit/Twig/{DataTableExtensionTest,DataTableThemeNodeTest,DataTableThemeTokenParserTest}.php` |
| Data collector | `tests/Unit/DataCollector/DataTableDataCollectorTest.php` |
| Utilities (String/Array/Form/RewindableGeneratorIterator) | `tests/Unit/Util/*Test.php` |

## References
| Topic | Location | Anchor |
|-------|----------|--------|
| Shared type machinery & block prefixes | [type-system.md](type-system.md) | §4, §7 |
| Lifecycle events | [core-data-table.md](core-data-table.md) | §5 |
| Default cache pool used by persistence | [persistence.md](persistence.md) | §2 |
| Config | `src/DependencyInjection/Configuration.php` | whole file |
