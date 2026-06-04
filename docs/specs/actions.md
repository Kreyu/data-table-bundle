# Actions (Implementation)

**Document type:** Implementation
**Source pinned to:** commit `2ace2d9`, version `0.17`

Actions are clickable controls rendered in three contexts. They use the shared type machinery
([type-system.md](type-system.md)); this spec covers contexts, the built-in types, URL/route
resolution, confirmation modals, and per-row closure evaluation.

## 1. Contexts

`ActionContext` enum (`src/Action/ActionContext.php:7-12`): `Global`, `Batch`, `Row`.

| Context | Added via | Rendered | View parent | Set at |
|---------|-----------|----------|-------------|--------|
| Global | `DataTable::addAction` | header/toolbar | `DataTableView` | `src/DataTable.php:366` |
| Batch | `DataTable::addBatchAction` | toolbar, operates on selected rows | `DataTableView` | `src/DataTable.php:408` |
| Row | `DataTable::addRowAction` | inside `ActionsColumnType` per row | `ColumnValueView` | `src/DataTable.php:450` |

The context determines whether closure-valued options may be used (only row actions have a per-row
value to evaluate against) and is exposed in the view as `context` + the `batch` boolean.

## 2. Built-in action types

All extend `AbstractActionType` (root `ActionType`); registered in `src/Resources/config/actions.php`.

| Type | Parent | Purpose / key options | Source |
|------|--------|-----------------------|--------|
| `LinkActionType` | ActionType | anchor; `href` (default `#`), `target` | `Type/LinkActionType.php:12` |
| `ButtonActionType` | Link | button styling; inherits href/target | `Type/ButtonActionType.php:11` |
| `FormActionType` | ActionType | wraps in a form; `method`, `action`, `button_attr`; generates a form id | `Type/FormActionType.php:13` |
| `ModalActionType` | ActionType | opens a modal; `route`+`route_params` or `href` | `Type/ModalActionType.php:14` |
| `DropdownActionType` | ActionType | container; `actions` (required), `with_caret`; propagates its context to items | `Type/Dropdown/DropdownActionType.php:15` |
| `LinkDropdownItemActionType` | Link | dropdown item | `Type/Dropdown/LinkDropdownItemActionType.php:10` |

Inherited options (`ActionType::configureOptions`, `src/Action/Type/ActionType.php:95-120`): `label`,
`translation_domain`, `translation_parameters`, `block_prefix`, `attr`, `icon`, `icon_attr`,
`confirmation`, `visible`, `variant`. Most accept a `Closure` evaluated against the row value for row
actions.

## 3. URL/route resolution

`ModalActionType::buildView` (`src/Action/Type/ModalActionType.php:43`) sets
`href = options['href'] ?? urlGenerator->generate(route, route_params)`; with neither it throws
`LogicException` (`:23-24`). `LinkActionType` and `FormActionType` evaluate their URL options the same
way, invoking closures only for row actions and throwing `LogicException` if a closure is used in a
non-row context (`ModalActionType.php:36-39`).

## 4. Confirmation modals

Setting `confirmation` truthy makes the action confirmable
(`src/Action/Type/ActionType.php:20`, `setConfirmable`). The option resolves to a structured config —
`translation_domain` (default `KreyuDataTable`), `label_title`, `label_description`, `label_confirm`,
`label_cancel`, and `type` ∈ {`danger`,`warning`,`info`} — via `resolveConfirmationOption`
(`:133-158`). A per-action identifier is generated as
`{table}--{context}-action--{name}[--{rowIndex}]` (`:54-69`).

## 5. View
`ActionView` (`src/Action/ActionView.php`) holds `vars` and a `DataTableView|ColumnValueView` parent.
`ActionType::buildView` (`:77-92`) populates label, block-prefix hierarchy, attrs, icon, confirmation,
context, `batch`, `visible`, `variant`; concrete types add `href`/`target`, `method`/`action`/
`form_id`, or `actions`/`with_caret`.

## Anti-Patterns (DO NOT)
| Don't | Do instead | Why |
|-------|-----------|-----|
| Read `getDataTable()` before attaching the action | Attach first | Throws `BadMethodCallException` (`src/Action/Action.php:31-38`) |
| Use a `Closure` option on a global/batch action | Use closures only for row actions | No per-row value exists; `LogicException` (`Type/ModalActionType.php:36-40`) |
| Configure a `ModalActionType` with neither `route` nor `href` | Provide one | `LogicException` (`:23-25`) |
| Build a `DropdownActionType` without `actions` | Provide the required option | OptionsResolver `required()` (`Type/Dropdown/DropdownActionType.php:48`) |
| Mutate an action builder after lock | Build, then read once | `BadMethodCallException` (`src/Action/ActionConfigBuilder.php:175`) |

## Error Handling Matrix
| Condition | Detection | Response | Exception | Source |
|-----------|-----------|----------|-----------|--------|
| Action not attached | guard | abort | `BadMethodCallException` | `src/Action/Action.php:34` |
| Modal without URL | guard | abort | `LogicException` | `src/Action/Type/ModalActionType.php:24` |
| Closure in non-row context | guard | abort | `LogicException` | `src/Action/Type/ModalActionType.php:38` |
| Unknown action type | registry | abort | `InvalidArgumentException` | `src/Action/ActionRegistry.php:62` |
| Circular action type | tracker | abort w/ path | `LogicException` | `src/Action/ActionRegistry.php:66` |

## Invariants & Existing Tests
| Invariant | Test |
|-----------|------|
| Builder/config/factory/registry incl. cycles | `tests/Unit/Action/{ActionBuilderTest,ActionConfigBuilderTest,ActionFactoryTest,ActionRegistryTest}.php` |
| Same-parent & recursive fixtures | `tests/Fixtures/Action/Type/*` |

## References
| Topic | Location | Anchor |
|-------|----------|--------|
| Shared type machinery | [type-system.md](type-system.md) | §3 |
| Row actions column | [columns.md](columns.md) | §3 (`ActionsColumnType`) |
| Block-prefix hierarchy for theming | [type-system.md](type-system.md) | §4 |
| Base action type | `src/Action/Type/ActionType.php` | `:77-158` |
