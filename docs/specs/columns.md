# Columns (Implementation)

**Document type:** Implementation
**Source pinned to:** commit `2ace2d9`, version `0.17`

Columns define header and value cells. They use the shared type machinery
([type-system.md](type-system.md)); this spec covers the column-specific contract, value resolution,
the built-in types, and the cross-cutting options (`sort`, `export`, `visible`, `personalizable`).

## 1. Contract & lifecycle

`ColumnInterface` (`src/Column/ColumnInterface.php:12-33`): `getName`, `getConfig`,
`get/setDataTable`, `getPropertyPath`, `getSortPropertyPath`, and four view factories —
`createHeaderView`, `createValueView`, `createExportHeaderView`, `createExportValueView`. Each view
factory delegates to the resolved type's `build*View` triple-delegation.

`ColumnType::buildColumn` (`src/Column/Type/ColumnType.php:31-50`) maps options onto the config:
property path (defaults to column name), sort property path (from `sort` option or property path),
priority, visibility, personalizable, sortable, exportable. Property paths are lazily computed and
fall back to the column name (`src/Column/Column.php:51-71`).

## 2. Value resolution pipeline

Two steps in `ColumnType` (`src/Column/Type/ColumnType.php:345-377`):

1. **Data extraction** (`getColumnDataFromRowData`): if a `getter` callable is set, call it; else if a
   property path resolves against an array/object, use Symfony PropertyAccessor; else pass the row
   data through unchanged.
2. **Value formatting** (`getColumnValueFromColumnData`): if a `formatter` callable is set, apply it;
   else return the data unchanged.

`buildValueView` (`:79-124`) stores `data` and `value`, handles translation
(key/domain/parameters), and merges `value_attr`. Export views run the same pipeline but the `export`
option (when an array) overrides `getter`/`property_path`/`formatter`/translation for export
(`:126-222`).

## 3. Built-in column types

Registered in `src/Resources/config/columns.php`. The **Parent** column below is the PHP class each
type extends. The *type-system* parent (`getParent()`) of **every** built-in column type is
`ColumnType` — `AbstractColumnType` and `AbstractDateTimeColumnType` are PHP base classes that do not
override `getParent()` (`src/Column/Type/AbstractColumnType.php:45-48`), so they are not themselves a
`getParent()` target.

| Type | Parent | Purpose / key options | Source |
|------|--------|-----------------------|--------|
| `TextColumnType` | AbstractColumnType | default; plain text | `Type/TextColumnType.php` |
| `NumberColumnType` | AbstractColumnType | `use_intl_formatter`, `intl_formatter_options` | `Type/NumberColumnType.php:17` |
| `MoneyColumnType` | AbstractColumnType | `currency` (required), `divisor`, intl options | `Type/MoneyColumnType.php:17` |
| `BooleanColumnType` | AbstractColumnType | `label_true`/`label_false`; domain `KreyuDataTable` | `Type/BooleanColumnType.php:17` |
| `LinkColumnType` | AbstractColumnType | `href` (default `#`), `target` | `Type/LinkColumnType.php:19` |
| `HtmlColumnType` | AbstractColumnType | `raw`, `strip_tags`, `allowed_tags` | `Type/HtmlColumnType.php:16` |
| `IconColumnType` | AbstractColumnType | `icon` (required), `icon_attr` | `Type/IconColumnType.php:14` |
| `EnumColumnType` | AbstractColumnType | renders PHP enums; optional translator | `Type/EnumColumnType.php:19` |
| `CollectionColumnType` | AbstractColumnType | `entry_type`/`entry_options`, `separator`, `separator_html` | `Type/CollectionColumnType.php:20` |
| `TemplateColumnType` | AbstractColumnType | `template_path` (required), `template_vars` | `Type/TemplateColumnType.php:16` |
| `AbstractDateTimeColumnType` | AbstractColumnType | PHP base for date/time; `format`, `timezone`; export date formatting | `Type/AbstractDateTimeColumnType.php:12` |
| `DateColumnType` | AbstractDateTimeColumnType | `format` default `d.m.Y` | `Type/DateColumnType.php:14` |
| `DateTimeColumnType` | AbstractDateTimeColumnType | `format` default `d.m.Y H:i:s` | `Type/DateTimeColumnType.php:14` |
| `DatePeriodColumnType` | AbstractDateTimeColumnType | `format`, `separator` | `Type/DatePeriodColumnType.php:16` |
| `CheckboxColumnType` | AbstractColumnType | batch-selection; `identifier_name` default `id` | `Type/CheckboxColumnType.php:22` |
| `ActionsColumnType` | AbstractColumnType | row actions; `actions`; `export=false`, no property path | `Type/ActionsColumnType.php:26` |

## 4. Cross-cutting options (`ColumnType::configureOptions`, `src/Column/Type/ColumnType.php:224-333`)

| Option | Type | Default | Effect |
|--------|------|---------|--------|
| `sort` | bool\|string | `false` | true → sortable by column name; string → custom sort path |
| `export` | bool\|array | `false` | false → excluded; array → per-export option overrides |
| `visible` | bool | `true` | column visibility |
| `personalizable` | bool | `true` | user may toggle/reorder it |
| `priority` | int | `0` | display order (higher first) |
| `property_path` | null\|bool\|string\|PropertyPath | `null` | value extraction path (defaults to name) |
| `getter` | null\|callable | `null` | overrides property path |
| `formatter` | null\|callable | `null` | transforms extracted data → value |

`ColumnConfigInterface` exposes `isSortable`, `isExportable`, `isPersonalizable`, `isVisible`,
`getPriority` (`src/Column/ColumnConfigInterface.php`).

`ColumnSortUrlGenerator` builds header sort links; it works on `'asc'`/`'desc'` strings and computes
the opposite direction (`src/Column/ColumnSortUrlGenerator.php:61-67`). It throws `LogicException`
without a current request (`:74-81`).

## Anti-Patterns (DO NOT)
| Don't | Do instead | Why |
|-------|-----------|-----|
| Read `getDataTable()` on a column before it's attached | Attach via builder/factory first | Throws `BadMethodCallException` (`src/Column/Column.php:35-42`) |
| Use a column for sorting without `sort: true`/path | Set the `sort` option | Sortability + sort path derive from it (`src/Column/Type/ColumnType.php:33-39`) |
| Put view-only HTML in the value and forget `export` overrides | Use `export` array to provide a plain value | Export reuses the pipeline but can override (`:126-222`) |
| Pad with redundant per-type docblocks | Rely on type + option names | Symfony comment policy |
| Assume hidden columns are exported | Check `getExportableColumns()` | Export respects personalization + `export` (`src/DataTable.php:222-262`) |

## Error Handling Matrix
| Condition | Detection | Response | Exception | Source |
|-----------|-----------|----------|-----------|--------|
| Column not attached | guard | abort | `BadMethodCallException` | `src/Column/Column.php:38` |
| Unknown column type | registry | abort | `InvalidArgumentException` | `src/Column/ColumnRegistry.php:62` |
| Circular column type | tracker | abort w/ path | `LogicException` | `src/Column/ColumnRegistry.php:64-67` |
| Invalid option | OptionsResolver | abort, wrapped | bundle `ExceptionInterface` | `src/Column/Type/ResolvedColumnType.php:56-62` |
| Sort URL without request | guard | abort | `LogicException` | `src/Column/ColumnSortUrlGenerator.php:77` |

## Invariants & Existing Tests
| Invariant | Test |
|-----------|------|
| Value/format pipeline per type | `tests/Unit/Column/Type/*Test.php` (Boolean, Checkbox, Collection, DateTime, Enum, Html, Link, Money, Number, Template, base `ColumnTypeTest`) |
| Builder/config/factory/registry | `tests/Unit/Column/{ColumnBuilderTest,ColumnConfigBuilderTest,ColumnFactoryTest,ColumnRegistryTest}.php` |
| Sort URL generation | `tests/Unit/Column/ColumnSortUrlGeneratorTest.php` |

## References
| Topic | Location | Anchor |
|-------|----------|--------|
| Shared type machinery | [type-system.md](type-system.md) | §3 |
| Row actions rendered in `ActionsColumnType` | [actions.md](actions.md) | §2 |
| Personalization of order/visibility | [personalization.md](personalization.md) | §1 |
| Base column type | `src/Column/Type/ColumnType.php` | `:31-377` |
