# Bridges (Implementation)

**Document type:** Implementation
**Source pinned to:** commit `2ace2d9`, version `0.17`

Bridges integrate third-party libraries: **Doctrine ORM** (data source + filters), **OpenSpout** and
**PhpSpreadsheet** (export formats). Each is auto-detected by class/interface existence — no separate
bundle to enable.

> **Path convention.** To keep tables readable, `Source` cells inside a `§` section are relative to
> that section's bridge root: §Doctrine → `src/Bridge/Doctrine/Orm/`, §OpenSpout →
> `src/Bridge/OpenSpout/`, §PhpSpreadsheet → `src/Bridge/PhpSpreadsheet/`. The Anti-Patterns and
> Error-Handling matrices use full `src/...` paths since they are read standalone.

## §Bridge architecture (shared pattern)

All bridges follow the same integration shape, so adding one (e.g. Elasticsearch) is mechanical:
1. **Library-optional:** a bridge's root/base type checks the library is present
   (`class_exists`/`interface_exists`) and throws `LogicException` if not — the type still exists in
   the container but fails fast when used without its dependency.
2. **Registered like any type:** bridge types are defined and tagged in
   `src/Resources/config/{filtration,exporter,core}.php`; data-source bridges add a
   `ProxyQueryFactoryInterface` tagged `kreyu_data_table.proxy_query.factory` (see §Registration).
3. **Resolved through the same registry** as core types ([type-system.md](type-system.md)) — a bridge
   type's `getParent()` points at the bridge's own root type, which parents the core root type.

## §Doctrine — ORM data source & filters

### Proxy query
`DoctrineOrmProxyQuery` (`src/Bridge/Doctrine/Orm/Query/DoctrineOrmProxyQuery.php:19-152`) wraps a
Doctrine `QueryBuilder` and implements `DoctrineOrmProxyQueryInterface` (extends
`ProxyQueryInterface`). It delegates unknown methods to the QueryBuilder via `__call` (`:37-40`).

- `sort` (`:47-61`): clears the `orderBy` DQL part, then `addOrderBy` per column, resolving paths via
  `AliasResolver`; skips `'none'`.
- `paginate` (`:63-69`): `setFirstResult` / `setMaxResults`.
- `getResult` (`:71-77`): builds a `Paginator` (via `PaginatorFactory`) and a `ResultSet` (via
  `DoctrineOrmResultSetFactory`).
- Pluggable factories: paginator, alias resolver, result-set factory; `hydrationMode`,
  `hints`, `batchSize` (validated > 0).

`AliasResolver` (`src/Bridge/Doctrine/Orm/Query/AliasResolver.php`): prefixes a simple property with
the root alias; leaves dotted/aliased paths untouched.

`PaginatorFactory` (`src/Bridge/Doctrine/Orm/Paginator/PaginatorFactory.php`): enables
`fetchJoinCollection` only for single-PK queries with joins; conservatively disables output walkers
only when safe (no HAVING, single FROM, scalar PK, no ORDER BY over a to-many join). `RuntimeException`
if the QueryBuilder has no root entity.

`DoctrineOrmResultSetFactory`: streams items in batches (default 5000) via
`RewindableGeneratorIterator` and reports counts from the Paginator.

### Filter application
`DoctrineOrmFilterHandler` (`src/Bridge/Doctrine/Orm/Filter/DoctrineOrmFilterHandler.php:22-69`):
requires a `DoctrineOrmProxyQueryInterface` (`UnexpectedTypeException` otherwise), builds parameters
(`ParameterFactory`), dispatches `PRE_SET_PARAMETERS`, binds them, builds a DQL expression
(`ExpressionFactory`), dispatches `PRE_APPLY_EXPRESSION`, then `andWhere(expression)`.

- `ExpressionFactory`: resolves path + operator → Doctrine expr (`eq/neq/gt/gte/lt/lte`, `like`/
  `notLike` for Contains/StartsWith/EndsWith, `in/notIn`, `between` with from/to).
- `ParameterFactory`: unique param names `{filter}_{id}`; wraps wildcards (`%v%`, `v%`, `%v`) per
  string operator; handles Between's from/to.
- Expression transformers (`ExpressionTransformerInterface`): `Lower`/`Upper`/`Trim` wrap both sides
  in the DQL function; `Callback` runs a closure. Applied by the `ApplyExpressionTransformers`
  subscriber in order trim → lower → upper → custom, driven by the filter's `trim`/`lower`/`upper`/
  `expression_transformers` options.

### Doctrine filter types
`DoctrineOrmFilterType` (root for the bridge; sets the handler + transformer subscriber) →
`AbstractDoctrineOrmFilterType` → concrete types:

| Type | Form type | Default / supported operators | Source |
|------|-----------|-------------------------------|--------|
| `StringFilterType` | TextType | Contains; Equals/NotEquals/Contains/NotContains/StartsWith/EndsWith | `Filter/Type/StringFilterType.php` |
| `NumericFilterType` | NumberType | comparison set | `Filter/Type/NumericFilterType.php` |
| `EntityFilterType` | EntityType | Equals/NotEquals/In/NotIn; auto `choice_value` = id | `Filter/Type/EntityFilterType.php` |
| `DateFilterType` | DateType (single_text) | Equals/NotEquals/Gt/Gte/Lt/Lte; date formatter | `Filter/Type/DateFilterType.php` |
| `DateTimeFilterType` | DateTimeType | as Date; datetime formatter | `Filter/Type/DateTimeFilterType.php` |
| `DateRangeFilterType` | DateRangeType | operator fixed to Between; transforms from/to | `Filter/Type/DateRangeFilterType.php` |
| `BooleanFilterType` | ChoiceType (Yes/No) | Equals/NotEquals | `Filter/Type/BooleanFilterType.php` |
| `DoctrineOrmCallbackFilterType` | — | deprecated since 0.15; use core `CallbackFilterType` | `Filter/Type/CallbackFilterType.php` |

Doctrine events: `DoctrineOrmFilterEvents::PRE_SET_PARAMETERS`, `PRE_APPLY_EXPRESSION`
(`src/Bridge/Doctrine/Orm/Event/`).

## §OpenSpout — CSV / XLSX / ODS

`OpenSpoutExporterType` (root) → `AbstractOpenSpoutExporterType`
(`src/Bridge/OpenSpout/Exporter/Type/AbstractOpenSpoutExporterType.php:22-118`): opens a writer to a
temp file, writes the header row (when `use_headers`), streams value rows as cells, closes, returns an
`ExportFile`. Style options: `header_row/cell_style`, `value_row/cell_style`. Concrete types provide
`getExtension`/`getWriterClass`/`getWriterOptions`:

| Type | Ext | Writer | Source |
|------|-----|--------|--------|
| `CsvExporterType` | csv | `Writer\CSV\Writer` | `Exporter/Type/CsvExporterType.php` |
| `XlsxExporterType` | xlsx | `Writer\XLSX\Writer` | `Exporter/Type/XlsxExporterType.php` |
| `OdsExporterType` | ods | `Writer\ODS\Writer` | `Exporter/Type/OdsExporterType.php` |

The base throws `LogicException` if OpenSpout is not installed. `AbstractExporterType` (deprecated
since 0.14) aliases `AbstractOpenSpoutExporterType`.

## §PhpSpreadsheet — CSV / XLS / XLSX / ODS / HTML / PDF

`PhpSpreadsheetExporterType` (root) → `AbstractPhpSpreadsheetExporterType`
(`src/Bridge/PhpSpreadsheet/Exporter/Type/AbstractPhpSpreadsheetExporterType.php:18-89`): builds a
`Spreadsheet` (header row from column labels, value rows from cell values; arrays imploded with
`', '`), gets the concrete writer, saves to a temp file, derives the extension via reflection on the
writer class.

| Type | Writer | Notes | Source |
|------|--------|-------|--------|
| `CsvExporterType` | `Writer\Csv` | delimiter/enclosure/encoding/etc. | `Exporter/Type/CsvExporterType.php` |
| `XlsxExporterType` | `Writer\Xlsx` | `office_2003_compatibility` | `Exporter/Type/XlsxExporterType.php` |
| `XlsExporterType` | `Writer\Xls` | — | `Exporter/Type/XlsExporterType.php` |
| `OdsExporterType` | `Writer\Ods` | — | `Exporter/Type/OdsExporterType.php` |
| `HtmlExporterType` | `Writer\Html` | inline css, sheet nav, etc. | `Exporter/Type/HtmlExporterType.php` |
| `PdfExporterType` | `Writer\Pdf\{Dompdf,Mpdf,Tcpdf}` | parent `HtmlExporterType`; `library` required; `orientation` | `Exporter/Type/PdfExporterType.php` |

The base throws `LogicException` if PhpSpreadsheet is not installed.

## §Registration
Bridge types are registered in `src/Resources/config/{filtration,exporter,core}.php` and tagged like
any type; proxy-query factories (`ArrayProxyQueryFactory`, `DoctrineOrmProxyQueryFactory`) in
`core.php:73-80`. Availability is gated by library presence at runtime, not by config flags.

## Anti-Patterns (DO NOT)
| Don't | Do instead | Why |
|-------|-----------|-----|
| Pass a non-Doctrine proxy query to a Doctrine filter | Use the Doctrine data source | `UnexpectedTypeException` (`src/Bridge/Doctrine/Orm/Filter/DoctrineOrmFilterHandler.php:33`) |
| Use a bridge export format without its library | Install OpenSpout / PhpSpreadsheet | Base type throws `LogicException` |
| Set `batchSize <= 0` on the proxy query | Use a positive batch size | `InvalidArgumentException` (`src/Bridge/Doctrine/Orm/Query/DoctrineOrmProxyQuery.php:117`) |
| Use the deprecated `DoctrineOrmCallbackFilterType` / bridge `AbstractExporterType` | Use core `CallbackFilterType` / `AbstractOpenSpoutExporterType` | Deprecated since 0.15 / 0.14 |

## Error Handling Matrix
| Condition | Detection | Response | Exception | Source |
|-----------|-----------|----------|-----------|--------|
| Wrong proxy query in Doctrine handler | `instanceof` | abort | `UnexpectedTypeException` | `src/Bridge/Doctrine/Orm/Filter/DoctrineOrmFilterHandler.php:33` |
| QueryBuilder with no root entity | check | abort | `RuntimeException` | `src/Bridge/Doctrine/Orm/Paginator/PaginatorFactory.php:18` |
| `Between` without from/to params | check | abort | `InvalidArgumentException` | `src/Bridge/Doctrine/Orm/Filter/ExpressionFactory/ExpressionFactory.php:36` |
| Invalid batch size | check | abort | `InvalidArgumentException` | `src/Bridge/Doctrine/Orm/Query/DoctrineOrmProxyQuery.php:117` |
| Library missing | `class/interface_exists` | abort | `LogicException` | bridge base types |

## Invariants & Existing Tests
| Invariant | Test |
|-----------|------|
| Doctrine handler/expression/parameter/transformers | `tests/Unit/Bridge/Doctrine/Orm/Filter/**` |
| Alias resolver, proxy query, result set, paginator | `tests/Unit/Bridge/Doctrine/Orm/Query/*`, `Paginator/PaginatorFactoryTest.php` |
| Active-filter formatters | `tests/Unit/Bridge/Doctrine/Orm/Filter/Formatter/*` |
| Doctrine fixtures (Car/Category/Product) | `tests/Unit/Bridge/Doctrine/Orm/Fixtures/Entity/*` |

## References
| Topic | Location | Anchor |
|-------|----------|--------|
| Backend-agnostic filter seam | [filters.md](filters.md) | §2 |
| ProxyQuery contract | [query-and-data-sources.md](query-and-data-sources.md) | §1 |
| Export pipeline these plug into | [exporters.md](exporters.md) | §1 |
| Doctrine proxy query | `src/Bridge/Doctrine/Orm/Query/DoctrineOrmProxyQuery.php` | `:19-152` |
