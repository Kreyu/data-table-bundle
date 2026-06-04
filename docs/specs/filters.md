# Filters (Implementation)

**Document type:** Implementation
**Source pinned to:** commit `2ace2d9`, version `0.17`

Filters narrow a query. They use the shared type machinery ([type-system.md](type-system.md)); this
spec covers the filter contract, the handler seam to the query backend, operators, filter forms, and
events. Doctrine ORM filter **implementations** live in [bridges.md](bridges.md) — this spec defines
the backend-agnostic seam.

## 1. Contract

`FilterInterface` (`src/Filter/FilterInterface.php:11-30`): `getName`, `getConfig`,
`get/setDataTable`, `getFormName` (dots → `__`), `getFormOptions`, `getQueryPath` (the `query_path`
option or the filter name), `handle(ProxyQueryInterface, FilterData)`, and
`createView(FilterData, DataTableView)`.

`FilterType` (`src/Filter/Type/FilterType.php:18-114`) is the root and defines the standard options:
`label`, `translation_domain`, `query_path`, `form_type` (default `TextType`), `form_options`,
`operator_form_type` (default `OperatorType`), `operator_form_options`, `default_operator`
(`Operator::Equals`), `supported_operators`, `operator_selectable` (false),
`active_filter_formatter`.

## 2. Application seam — handlers

The core deliberately keeps filtering **out** of `ProxyQueryInterface`
(`src/Query/ProxyQueryInterface.php:10-17` exposes only `sort`/`paginate`/`getResult`). A filter
applies itself through a `FilterHandlerInterface` (`src/Filter/FilterHandlerInterface.php:9-12`):

```php
handle(ProxyQueryInterface $query, FilterData $data, FilterInterface $filter): void;
```

`Filter::handle` (`src/Filter/Filter.php:74-81`) dispatches `FilterEvents::PRE_HANDLE`, invokes the
configured handler, then dispatches `POST_HANDLE`. The handler is responsible for narrowing the query
for **its** backend — e.g. `DoctrineOrmFilterHandler` casts to `DoctrineOrmProxyQueryInterface` (see
[bridges.md](bridges.md)). This is what lets the same filter type work across data sources.

Built-in self-handling types: `SearchFilterType` (`callable handler` option; `src/Filter/Type/SearchFilterType.php`)
and `CallbackFilterType` (`callback` option; `src/Filter/Type/CallbackFilterType.php`). A custom
filter type must call `$builder->setHandler(...)` in `buildFilter` or `getHandler()` throws
`BadMethodCallException` (`src/Filter/FilterConfigBuilder.php:134-141`).

## 3. Operators

`Operator` enum (`src/Filter/Operator.php:7-41`): `Equals`, `NotEquals`, `Contains`, `NotContains`,
`In`, `NotIn`, `GreaterThan`, `GreaterThanEquals`, `LessThan`, `LessThanEquals`, `StartsWith`,
`EndsWith`, `Between`; each has a `getLabel()`. The configured `default_operator` is always merged
into `getSupportedOperators()` so it is never unselectable
(`src/Filter/FilterConfigBuilder.php:218-221`). `OperatorType` is a Symfony `EnumType` over `Operator`
labelled via `getLabel` (`src/Filter/Form/Type/OperatorType.php`).

## 4. Filter data & forms

- `FilterData` (`src/Filter/FilterData.php`): `value` + optional `Operator`; `fromArray` normalizes a
  string operator to the enum; `hasValue()` treats null / `''` / `[]` as empty.
- `FiltrationData` (`src/Filter/FiltrationData.php`): map of filter name → `FilterData`;
  `appendMissingFilters`/`removeRedundantFilters` keep it in sync with the table; `hasActiveFilters`.
- Forms: `FiltrationDataType` adds a `FilterDataType` per filter; `FilterDataType` adds a `value`
  field (empty string pre-submitted to null) and, only when `operator_selectable`, an `operator`
  field (`src/Filter/Form/Type/`).

## 5. Events & extensions

- `FilterEvents::PRE_HANDLE` / `POST_HANDLE` (`src/Filter/Event/FilterEvents.php`);
  `PreHandleEvent`/`PostHandleEvent` carry a **mutable** `FilterData` so listeners can rewrite input.
- The event dispatcher is wrapped in `ImmutableEventDispatcher` once read, so listeners must be added
  during `buildFilter` (`src/Filter/FilterConfigBuilder.php:52-59`).
- `FilterTypeExtensionInterface` follows the shared extension pattern.

## 6. Built-in core filter types
`FilterType` (root), `SearchFilterType`, `CallbackFilterType`
(`src/Resources/config/filtration.php:71-83`). Backend-specific types (`StringFilterType`,
`NumericFilterType`, `EntityFilterType`, date/range/boolean) are Doctrine ORM types → see
[bridges.md](bridges.md).

## Anti-Patterns (DO NOT)
| Don't | Do instead | Why |
|-------|-----------|-----|
| Add filtering methods to `ProxyQueryInterface` | Apply via a `FilterHandlerInterface` | Keeps filters backend-agnostic (`src/Filter/FilterHandlerInterface.php`) |
| Build a custom filter type without `setHandler()` | Set a handler in `buildFilter` | `getHandler()` throws otherwise (`src/Filter/FilterConfigBuilder.php:137`) |
| Treat empty string as a real filter value | Rely on `hasValue()` / pre-submit-to-null | Avoids matching everything (`src/Filter/FilterData.php:60-63`, `Form/Type/FilterDataType.php:24-28`) |
| Add filter event listeners after the filter is built | Add them in `buildFilter` | Dispatcher becomes immutable (`src/Filter/FilterConfigBuilder.php:52-59`) |
| Omit the default operator from `supported_operators` and assume it's gone | It's always merged in | `getSupportedOperators()` guarantees it (`:218-221`) |

## Error Handling Matrix
| Condition | Detection | Response | Exception | Source |
|-----------|-----------|----------|-----------|--------|
| Filter not attached | guard | abort | `BadMethodCallException` | `src/Filter/Filter.php:38` |
| No handler set | guard | abort | `BadMethodCallException` | `src/Filter/FilterConfigBuilder.php:137` |
| Unknown filter type | registry | abort | `InvalidArgumentException` | `src/Filter/FilterRegistry.php:62` |
| Circular filter type | tracker | abort w/ path | `LogicException` | `src/Filter/FilterRegistry.php:66` |
| Wrong proxy query for handler | `instanceof` | abort | `UnexpectedTypeException` | `src/Bridge/Doctrine/Orm/Filter/DoctrineOrmFilterHandler.php:33` |
| Clear-filter URL without request | guard | abort | `LogicException` | `src/Filter/FilterClearUrlGenerator.php:72` |

## Invariants & Existing Tests
| Invariant | Test |
|-----------|------|
| Filter handle/event flow | `tests/Unit/Filter/FilterTest.php` |
| Builder/config/factory/registry | `tests/Unit/Filter/{FilterBuilderTest,FilterConfigBuilderTest,FilterFactoryTest,FilterRegistryTest}.php` |
| Clear-filter URL | `tests/Unit/Filter/FilterClearUrlGeneratorTest.php` |
| Doctrine filter application | `tests/Unit/Bridge/Doctrine/Orm/Filter/*` |

## References
| Topic | Location | Anchor |
|-------|----------|--------|
| Shared type machinery | [type-system.md](type-system.md) | §3 |
| Doctrine ORM filter types & expression building | [bridges.md](bridges.md) | §Doctrine |
| Query abstraction the handler narrows | [query-and-data-sources.md](query-and-data-sources.md) | §1 |
| Base filter type | `src/Filter/Type/FilterType.php` | `:18-114` |
