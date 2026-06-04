# Exporters (Implementation)

**Document type:** Implementation
**Source pinned to:** commit `2ace2d9`, version `0.17`

Exporters turn a data table view into a downloadable file. They use the shared type machinery
([type-system.md](type-system.md)); concrete format exporters live in the bridges
([bridges.md](bridges.md)). This spec covers the export pipeline, contract, and strategy.

## 1. Pipeline

`DataTable::export(?ExportData)` (`src/DataTable.php:623-655`): throws if exporting disabled, clones
the table, and — when the strategy is `IncludeAll` — re-paginates to show every row; optionally drops
personalization; selects the exporter (named or first registered); calls
`Exporter::export(exportView, filename)`.

`Exporter::export` (`src/Exporter/Exporter.php:46-49`) delegates to the resolved type's
`export(DataTableView, ExporterInterface, filename, options)` and returns an `ExportFile`.
`ExportFile` (`src/Exporter/ExportFile.php`) extends Symfony's `File` and carries an optional
download filename.

## 2. Contract & base types

`ExporterInterface` (`src/Exporter/ExporterInterface.php:10-21`): `getName`, `getConfig`,
`get/setDataTable`, `export(DataTableView, filename='export'): ExportFile`.

- `ExporterType` (root, `src/Exporter/Type/ExporterType.php:14-44`): `export()` throws
  `LogicException` (abstract); options `use_headers` (true), `label`, `tempnam_dir`
  (`sys_get_temp_dir()`), `tempnam_prefix` (`exporter_`). `getParent()` → null.
- `AbstractExporterType` (`src/Exporter/Type/AbstractExporterType.php:12-36`): parent `ExporterType`;
  `getTempnam()` helper.
- `CallbackExporterType` (`src/Exporter/Type/CallbackExporterType.php`): `callback` (required) — full
  control: `callable(DataTableView, ExporterInterface, filename, options): ExportFile`.

Concrete CSV/XLSX/ODS/XLS/PDF/HTML exporters are provided by the OpenSpout and PhpSpreadsheet bridges
(see [bridges.md](bridges.md)).

## 3. Export data & strategy

- `ExportData` (`src/Exporter/ExportData.php`): `filename`, `exporter`, `strategy`,
  `includePersonalization`; `fromArray`/`fromDataTable` factories.
- `ExportStrategy` enum (`src/Exporter/ExportStrategy.php`): `IncludeCurrentPage`,
  `IncludeAll` (+ deprecated aliases normalized via `getNonDeprecatedCase()`).
- The export form is built by the data table when exporting is enabled (see
  [core-data-table.md](core-data-table.md) §4); submission sets `ExportData`
  (`src/Request/HttpFoundationRequestHandler.php:117-132`).

## Anti-Patterns (DO NOT)
| Don't | Do instead | Why |
|-------|-----------|-----|
| Call `export()` when exporting is disabled | Enable exporting / guard with `isExportingEnabled()` | Throws `RuntimeException`/`LogicException` (`src/DataTable.php:626`) |
| Instantiate the abstract `ExporterType` directly | Use a concrete bridge type or `CallbackExporterType` | Base `export()` throws `LogicException` (`src/Exporter/Type/ExporterType.php:18`) |
| Assume a bridge format is available | Install the library | Bridge base types throw if the library is missing (see [bridges.md](bridges.md)) |
| Export hidden/non-export columns | Use `getExportableColumns()` | Honors `export` option + personalization (`src/DataTable.php:222-262`) |

## Error Handling Matrix
| Condition | Detection | Response | Exception | Source |
|-----------|-----------|----------|-----------|--------|
| Exporting disabled | feature flag | abort | `RuntimeException` | `src/DataTable.php:626` |
| Abstract type exported | base method | abort | `LogicException` | `src/Exporter/Type/ExporterType.php:18` |
| Exporter not attached | guard | abort | `BadMethodCallException` | `src/Exporter/Exporter.php:33` |
| Unknown exporter type | registry | abort | `InvalidArgumentException` | `src/Exporter/ExporterRegistry.php` |
| Builder used after lock | locked flag | abort | `BadMethodCallException` | `src/Exporter/ExporterConfigBuilder.php:142-143` |
| Missing export library | `class_exists`/`interface_exists` | abort | `LogicException` | bridge base types — [bridges.md](bridges.md) |

## Invariants & Existing Tests
| Invariant | Test |
|-----------|------|
| Builder/config/factory/registry | `tests/Unit/Exporter/{ExporterBuilderTest,ExporterConfigBuilderTest,ExporterFactoryTest,ExporterRegistryTest}.php` |
| Concrete format writers | `tests/Unit/Bridge/...` (OpenSpout / PhpSpreadsheet — see [bridges.md](bridges.md)) |

## References
| Topic | Location | Anchor |
|-------|----------|--------|
| Shared type machinery | [type-system.md](type-system.md) | §3 |
| Concrete format exporters | [bridges.md](bridges.md) | §OpenSpout, §PhpSpreadsheet |
| Export view (exportable columns) | [core-data-table.md](core-data-table.md) | §4 |
| Exporter | `src/Exporter/Exporter.php` | `:46-49` |
