# Personalization (Implementation)

**Document type:** Implementation
**Source pinned to:** commit `2ace2d9`, version `0.17`

Personalization lets a user reorder and show/hide columns; the state is persistable
([persistence.md](persistence.md)). Disabled by default in config.

## 1. Model

`PersonalizationData` (`src/Personalization/PersonalizationData.php`): a map of column name →
`PersonalizationColumnData`. `PersonalizationColumnData`
(`src/Personalization/PersonalizationColumnData.php:14-19`): `name`, `priority` (int, default 0),
`visible` (bool, default true). `fromArray` validates the schema and auto-fills `name` from the array
key; a BC layer maps a legacy `order` key to `priority` (`:62-65`).

`PersonalizationData` keeps itself in sync with the table: `addMissingColumns` /
`removeRedundantColumns` (called from `DataTable::personalize`, `src/DataTable.php:611-612`), and
rejects adding a non-personalizable column (`InvalidArgumentException`,
`PersonalizationData.php:98-100`).

Personalization state is one of the four `PersistenceContext::Personalization` cases and is
optionally stored per user via the `PersistenceAdapterInterface` — see [persistence.md](persistence.md).

## 2. Application to the view

`DataTable::getColumns` sorts by priority, using the personalization priority when personalization is
enabled and the column is personalizable, else the column's default priority
(`src/DataTable.php:174-194`). `getVisibleColumns` filters by the personalization `visible` flag the
same way (`:196-207`); `getHiddenColumns` is the inverse; `getExportableColumns` additionally honors
the `export` option (`:222-262`).

## 3. Form

`PersonalizationDataType` (`src/Personalization/Form/Type/PersonalizationDataType.php`): a `columns`
`CollectionType` of `PersonalizationColumnDataType` with `allow_add: true`. `finishView` copies each
column's label/translation domain/parameters from the **non-personalized** header row, and requires
the `data_table_view` option (else `LogicException`). `PersonalizationColumnDataType`: three
`HiddenType` fields (`name`, `priority`, `visible`) — JavaScript submits them — with a
`CallbackTransformer` converting `visible` between int (form) and bool (model).

Submission flow: `HttpFoundationRequestHandler::personalize` submits the form and calls
`DataTable::personalize` on valid data (`src/Request/HttpFoundationRequestHandler.php:100-115`);
`personalize()` fires `PRE/POST_PERSONALIZE` and persists when enabled (`src/DataTable.php:599-621`).

## Anti-Patterns (DO NOT)
| Don't | Do instead | Why |
|-------|-----------|-----|
| Add a non-personalizable column to personalization data | Respect `isPersonalizable()` | Throws `InvalidArgumentException` (`PersonalizationData.php:98-100`) |
| Build the personalization form without `data_table_view` | Pass the view | `finishView` needs labels; `LogicException` (`Form/Type/PersonalizationDataType.php:31`) |
| Let personalization drift from current columns | Rely on add-missing/remove-redundant | Keeps state valid (`src/DataTable.php:611-612`) |
| Assume personalization is on | It defaults **off** | Config default `enabled: false` ([infrastructure.md](infrastructure.md) §Config) |

## Error Handling Matrix
| Condition | Detection | Response | Exception | Source |
|-----------|-----------|----------|-----------|--------|
| Non-personalizable column added | guard | abort | `InvalidArgumentException` | `src/Personalization/PersonalizationData.php:98-100` |
| Form built without `data_table_view` | option check | abort | `LogicException` | `src/Personalization/Form/Type/PersonalizationDataType.php:31` |
| Personalization persistence misconfigured | null check | abort | `RuntimeException` | `src/DataTable.php:1007,1027` |

## Invariants & Existing Tests
| Invariant | Test |
|-----------|------|
| Column ordering/visibility applied to columns | `tests/Unit/DataTableTest.php` (column accessors) |
| Personalization persistence round-trip | `tests/Unit/Persistence/*` + `tests/Unit/DataTableTest.php` |

> No dedicated `tests/Unit/Personalization/*` directory exists at the pinned commit; personalization
> behavior is exercised through `DataTable` and persistence tests. Recorded as an observation, not a
> gap to pad with invented tests.

## References
| Topic | Location | Anchor |
|-------|----------|--------|
| Persistence of personalization state | [persistence.md](persistence.md) | §1, §4 |
| Column `personalizable`/`priority`/`visible` options | [columns.md](columns.md) | §4 |
| Personalization form rendering | [infrastructure.md](infrastructure.md) | §Theming |
| Personalization data | `src/Personalization/PersonalizationData.php` | — |
