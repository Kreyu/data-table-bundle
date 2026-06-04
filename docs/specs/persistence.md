# Persistence (Implementation)

**Document type:** Implementation
**Source pinned to:** commit `2ace2d9`, version `0.17`

Persistence stores a user's sorting, pagination, filtration, and personalization state between
requests so a table restores on return.

## 1. What is persisted, keyed by what

`PersistenceContext` enum (`src/Persistence/PersistenceContext.php:7-13`): `Sorting`, `Pagination`,
`Filtration`, `Personalization`. Each context persists its data object (`SortingData`,
`PaginationData`, `FiltrationData`, `PersonalizationData`).

Storage key = `urlencode(table name + context prefix + subject identifier)`
(`CachePersistenceAdapter::getCacheKey`, `src/Persistence/CachePersistenceAdapter.php:50-57`).

## 2. Adapter

`PersistenceAdapterInterface` (`src/Persistence/PersistenceAdapterInterface.php:9-14`):
`read(dataTable, subject): mixed`, `write(dataTable, subject, data): void`.

`CachePersistenceAdapter` (`src/Persistence/CachePersistenceAdapter.php`): backed by Symfony
`CacheInterface`, one instance per context (prefix). When the cache is `TagAwareCacheInterface`,
items are tagged `kreyu_data_table_persistence_<subjectId>` so `CachePersistenceClearer` can
invalidate all of a subject's state at once; non-tag-aware caches degrade gracefully. The default
cache pool `kreyu_data_table.persistence.cache.default` is a tagged filesystem pool registered in
`prepend()` ([infrastructure.md](infrastructure.md) §DI).

## 3. Subject (the "per user" key)

`PersistenceSubjectInterface` (`src/Persistence/PersistenceSubjectInterface.php`):
`getDataTablePersistenceIdentifier(): string`. `PersistenceSubjectProviderInterface` resolves one,
throwing `PersistenceSubjectNotFoundException` on failure.

| Provider | Resolves to | Source |
|----------|-------------|--------|
| `TokenStoragePersistenceSubjectProvider` | the user if it implements `PersistenceSubjectInterface` (wrapped in `PersistenceSubjectAggregate`), else `UserInterface::getUserIdentifier()` | `src/Persistence/TokenStoragePersistenceSubjectProvider.php:17-36` |
| `StaticPersistenceSubjectProvider` | a fixed identifier (default `static`) for anonymous contexts | `src/Persistence/StaticPersistenceSubjectProvider.php` |

Each context can use a **different** provider (`DataTable::getPersistenceSubject`,
`src/DataTable.php:1013-1030`).

## 4. Lifecycle wiring

- **Read:** `initialize()` calls `getInitial{Pagination,Sorting,Filtration,Personalization}Data`;
  when persistence is enabled and adapter+provider are configured, each reads from the adapter, else
  uses the config default, else an empty value (`src/DataTable.php:130-157`, `898-961`). The applied
  operation passes `persistence:false` to avoid writing back what was just read.
- **Write:** each `paginate`/`sort`/`filter`/`personalize` writes via `setPersistenceData` when
  `persistence:true` and the context's persistence is enabled
  (`src/DataTable.php:526,555,590,615,985-995`).

Persistence is enabled per context in config (default **off**) — see [infrastructure.md](infrastructure.md)
§Config tree. `DefaultConfigurationPass` defaults the adapter to the cache adapter and the provider to
token-storage when those services exist (`src/DependencyInjection/DefaultConfigurationPass.php:16-34`).

## Anti-Patterns (DO NOT)
| Don't | Do instead | Why |
|-------|-----------|-----|
| Enable persistence without an adapter + subject provider | Configure both (or let the compiler pass default them) | `RuntimeException` at read/write (`src/DataTable.php:1007,1027`) |
| Write persisted state back during init | Apply initial data with `persistence:false` | Avoids redundant writes (`src/DataTable.php:138-152`) |
| Assume `CachePersistenceClearer` works on any cache | Use a `TagAwareCacheInterface` pool | Clearing needs tags; else `LogicException` (`src/Persistence/CachePersistenceClearer.php:24`) |
| Share cache keys across subjects | Rely on the subject-id in the key/tag | Isolation per subject (`src/Persistence/CachePersistenceAdapter.php:50-57`) |

## Error Handling Matrix
| Condition | Detection | Response | Exception | Source |
|-----------|-----------|----------|-----------|--------|
| Persistence used while disabled | feature flag | abort | `RuntimeException` | `src/DataTable.php:976` |
| Adapter null while enabled | null check | abort | `RuntimeException` | `src/DataTable.php:1007` |
| Subject provider null while enabled | null check | abort | `RuntimeException` | `src/DataTable.php:1026` |
| Subject cannot be resolved | provider | abort | `PersistenceSubjectNotFoundException` | `src/Persistence/TokenStoragePersistenceSubjectProvider.php:35` |
| Clear on non-tag-aware cache | type check | abort | `LogicException` | `src/Persistence/CachePersistenceClearer.php:24` |

## Invariants & Existing Tests
| Invariant | Test |
|-----------|------|
| Cache read/write/key | `tests/Unit/Persistence/CachePersistenceAdapterTest.php` |
| Tag-based clearing | `tests/Unit/Persistence/CachePersistenceClearerTest.php` |
| Subject providers | `tests/Unit/Persistence/{StaticPersistenceSubjectProviderTest,TokenStoragePersistenceSubjectProviderTest}.php` |

## References
| Topic | Location | Anchor |
|-------|----------|--------|
| When read/write happen in the lifecycle | [core-data-table.md](core-data-table.md) | §1, §5 |
| Personalization data that is persisted | [personalization.md](personalization.md) | §1 |
| Config tree & default cache pool | [infrastructure.md](infrastructure.md) | §DI |
| Cache adapter | `src/Persistence/CachePersistenceAdapter.php` | `:31-71` |
