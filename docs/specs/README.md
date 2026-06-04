# Architecture Specs

Reverse-engineered **technical/architecture** specifications for `kreyu/data-table-bundle` —
how the bundle is built internally, with file+line citations. Pinned to commit `2ace2d9`
(2026-04-12), version `0.17`.

> These describe **how it works**. For **how to use** the bundle, see the user-facing VitePress site
> under [`docs/src/`](../src/). On disagreement, the code at the pinned commit wins.

## Reading order

1. [architecture-overview.md](architecture-overview.md) — *(Strategic)* what/why, subsystem map, lifecycle spine
2. [type-system.md](type-system.md) — the shared type/resolved-type/extension/registry/factory/builder pattern (read before any component spec)
3. [core-data-table.md](core-data-table.md) — `DataTable` runtime, request handling, views, events, parameter naming

## Subsystems
- [columns.md](columns.md)
- [filters.md](filters.md)
- [actions.md](actions.md)
- [exporters.md](exporters.md)
- [query-and-data-sources.md](query-and-data-sources.md) — ProxyQuery, ResultSet, pagination, sorting
- [persistence.md](persistence.md)
- [personalization.md](personalization.md)
- [bridges.md](bridges.md) — Doctrine ORM, OpenSpout, PhpSpreadsheet
- [infrastructure.md](infrastructure.md) — DI/config, Twig & theming, profiler, maker, events, exceptions

## Method note
Each implementation spec carries Anti-Patterns and an Error-Handling Matrix derived from the actual
guards in the code. The "Invariants & Existing Tests" sections point to the **real** test suite under
[`tests/`](../../tests/) rather than inventing greenfield test tables for already-tested code.

## Maintenance
Specs are pinned to a commit/version in each header. Re-pin and re-verify citations on minor releases;
between versions, the linked [`CHANGELOG.md`](../../CHANGELOG.md) is authoritative for what changed.
Where a spec disagrees with the code at a later commit, the code wins — update the spec.
