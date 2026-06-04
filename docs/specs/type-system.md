# Shared Type System (Implementation)

**Document type:** Implementation
**Source pinned to:** commit `2ace2d9`, version `0.17`

Five subsystems — data tables, columns, filters, actions, exporters — share one type-system design.
This document specifies it **once**. The per-subsystem specs reference this and only document their
own type list, options, and view variables. (Single-source-of-truth: do not restate this machinery
in those specs.)

## 1. The five roles

| Role | Purpose | Example (data table) |
|------|---------|----------------------|
| **Type** | Stateless definition: declares options, builds the component & its view | `DataTableType`, `AbstractDataTableType` |
| **Resolved type** | Type + parent chain + extensions, with a cached `OptionsResolver` | `ResolvedDataTableType` |
| **Type extension** | Cross-cutting decorator applied to one or more named types | `DataTableTypeExtensionInterface` |
| **Registry** | Resolves & caches resolved types; detects circular `getParent()` | `DataTableRegistry` |
| **Factory** | Public entry point: name → builder → instance | `DataTableFactory` |
| **Builder → Config → Instance** | Mutable build → locked immutable config → runtime object | `DataTableBuilder` → config → `DataTable` |

## 2. Type contract

Every `*TypeInterface` exposes the same shape (names vary per subsystem):

```php
buildX(XBuilderInterface $builder, array $options): void;   // configure the component
buildView(XView $view, X $component, array $options): void; // populate template vars
configureOptions(OptionsResolver $resolver): void;          // declare + constrain options
getParent(): ?string;                                       // FQCN of parent type, or null at root
getBlockPrefix()/getName(): string;                         // identity / Twig block prefix
```

- `DataTableTypeInterface` (`src/Type/DataTableTypeInterface.php:12-37`) also has
  `buildExportView()`.
- `ColumnTypeInterface` (`src/Column/Type/ColumnTypeInterface.php:13-33`) adds header/value and
  export-header/export-value build methods.
- `FilterTypeInterface` (`src/Filter/Type/FilterTypeInterface.php:13-27`),
  `ActionTypeInterface` (`src/Action/Type/ActionTypeInterface.php:12-26`),
  `ExporterTypeInterface` (`src/Exporter/Type/ExporterTypeInterface.php:13-27`).

`Abstract*Type` base classes provide no-op defaults and derive their name/block-prefix from the FQCN
via `StringUtil::fqcnToShortName()`, and return the subsystem root type from `getParent()`
(e.g. `src/Type/AbstractDataTableType.php:31-39`). Root types (`DataTableType`, `ColumnType`,
`FilterType`, `ActionType`, `ExporterType`) return `null` from `getParent()`.

## 3. Resolved type — the triple-delegation pattern

`Resolved*Type` wraps an inner type, an array of extensions, and an optional resolved parent
(`src/Type/ResolvedDataTableType.php:18-127`). Every `build*` call delegates in a fixed order:

```
1. parent resolved type   (if any)   →
2. inner type                         →
3. each extension (registration order)
```

`getOptionsResolver()` (`src/Type/ResolvedDataTableType.php:109-126`) clones the parent's resolver
(or creates a fresh one at the root), then calls `configureOptions()` on the inner type and each
extension, and **caches** the result. `createBuilder()` resolves the passed options through this
resolver and wraps `OptionsResolver` exceptions with the offending type's name
(`src/Type/ResolvedDataTableType.php:55-64`).

The same pattern recurs in `ResolvedColumnType`, `ResolvedFilterType`, `ResolvedActionType`,
`ResolvedExporterType`.

## 4. Block-prefix hierarchy (Twig theming hook)

A resolved type can produce its block-prefix hierarchy by walking the parent chain
(e.g. `src/Action/Type/ResolvedActionType.php:32-50`): `ButtonActionType` → `['button','link','action']`.
A per-component `block_prefix` option, if set, is prepended. Twig resolves the first theme block that
matches any prefix — this is what makes per-type template overrides work
(see [infrastructure.md](infrastructure.md) §Theming).

## 5. Registry — resolution & circular detection

`getType(fqcn)` memoizes resolved types (`src/DataTableRegistry.php:60-97`). `resolveType()`:
tracks the in-flight chain in `$checkedTypes`, throws `LogicException` (full path) on a cycle,
recursively resolves the parent, attaches extensions grouped by extended type FQCN, and builds the
resolved type via a `Resolved*TypeFactory`. Type/extension/factory objects are validated with
`UnexpectedTypeException`.

Extensions are grouped by `getExtendedTypes()` (each extension lists the type FQCNs it targets) at
registration (`src/DataTableRegistry.php:112-125`).

## 6. Factory — public entry point

`create(type, data, options)` → `createBuilder` → `getDataTable()`
(`src/DataTableFactory.php:17-52`). `createNamedBuilder` resolves the type via the registry, calls
`Resolved*Type::createBuilder`, then immediately invokes `type->buildDataTable(builder, options)` so
the returned builder is already populated. For data tables only, the factory first wraps the raw
`$data` into a `ProxyQuery` using the first registered `ProxyQueryFactory` whose `supports()` returns
true (see [query-and-data-sources.md](query-and-data-sources.md)).

## 7. Registration via DI (autoconfiguration)

`KreyuDataTableExtension` maps each interface to a tag
(`src/DependencyInjection/KreyuDataTableExtension.php:29-42`); registries consume
`tagged_iterator(...)`. Implementing a type/extension interface is sufficient to register it — see
[infrastructure.md](infrastructure.md) §DI.

## Assumptions
| Assumption | If wrong, then… |
|------------|-----------------|
| The five subsystems keep identical delegation semantics. Verified for data-table, column, filter, action, exporter resolved types at the pinned commit. | A subsystem that diverges needs its difference documented in its own spec. |

## Anti-Patterns (DO NOT)
| Don't | Do instead | Why |
|-------|-----------|-----|
| `new SomeColumnType()` / instantiate types directly in app code | Add via builder/options (`addColumn('x', SomeType::class, [...])`) | Types must be resolved through the registry so parents + extensions apply (`src/DataTableFactory.php:45-49`) |
| Mutate a builder after reading its config | Build fully, then read once | Builders lock and throw `BadMethodCallException` (`src/DataTableConfigBuilder.php:860-867`) |
| Create a type whose `getParent()` cycles back to itself | Keep parent chains acyclic | Registry throws `LogicException` with the path (`src/DataTableRegistry.php:79-82`) |
| Use PHP `extends` to share built-in type behavior | Use `getParent()` composition | Built-in types are `final` since 0.17 (CHANGELOG.md) |
| Assume extension order is arbitrary | Treat extensions as ordered after the inner type | Delegation is parent → inner → extensions in registration order (`src/Type/ResolvedDataTableType.php:76-96`) |

## Error Handling Matrix
| Condition | Detection | Response | Exception | Source |
|-----------|-----------|----------|-----------|--------|
| Unknown type FQCN | Registry lookup miss | Abort resolution | `InvalidArgumentException` | `src/DataTableRegistry.php:77` |
| Circular `getParent()` | `$checkedTypes` tracker | Abort with path | `LogicException` | `src/DataTableRegistry.php:81` |
| Non-type passed as type | `instanceof` check | Abort | `UnexpectedTypeException` | `src/DataTableRegistry.php:105` |
| Invalid option value/type | `OptionsResolver` in `createBuilder` | Abort, wrapped w/ type name | bundle `ExceptionInterface` | `src/Type/ResolvedDataTableType.php:57-61` |
| Builder used after lock | `locked` flag | Reject mutation | `BadMethodCallException` | `src/DataTableConfigBuilder.php:860-867` |

## Invariants & Existing Tests
Reverse-engineered spec of existing, tested code — pointers to the real suite rather than invented
greenfield tests (recorded reason: padding invented tests for tested code violates the no-fluff rule).

| Invariant | Test |
|-----------|------|
| Registry resolves types and rejects cycles / same-parent | `tests/Unit/DataTableRegistryTest.php`, `tests/Unit/Column/ColumnRegistryTest.php`, `tests/Unit/Filter/FilterRegistryTest.php`, `tests/Unit/Action/ActionRegistryTest.php`, `tests/Unit/Exporter/ExporterRegistryTest.php` |
| Factory builds populated builders | `tests/Unit/DataTableFactoryTest.php`, `tests/Unit/Column/ColumnFactoryTest.php`, `tests/Unit/Filter/FilterFactoryTest.php`, `tests/Unit/Action/ActionFactoryTest.php`, `tests/Unit/Exporter/ExporterFactoryTest.php` |
| Config builders lock after read | `tests/Unit/DataTableConfigBuilderTest.php`, `tests/Unit/Column/ColumnConfigBuilderTest.php`, `tests/Unit/Filter/FilterConfigBuilderTest.php`, `tests/Unit/Action/ActionConfigBuilderTest.php`, `tests/Unit/Exporter/ExporterConfigBuilderTest.php` |
| Recursive/same-parent fixtures exist per subsystem | `tests/Fixtures/*/Type/Recursive*`, `tests/Fixtures/*/Type/*WithSameParentType.php` |

## References
| Topic | Location | Anchor |
|-------|----------|--------|
| Overview & subsystem map | [architecture-overview.md](architecture-overview.md) | §3 |
| Core lifecycle that consumes resolved types | [core-data-table.md](core-data-table.md) | §1 |
| DI tags & autoconfiguration | [infrastructure.md](infrastructure.md) | §DI |
| Resolved data-table type | `src/Type/ResolvedDataTableType.php` | `:18-127` |
| Registry | `src/DataTableRegistry.php` | `:60-138` |
