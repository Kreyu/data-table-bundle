# Keyset Pagination (Design Proposal)

**Document type:** Implementation — **design proposal + phased plan** (not yet a full executable spec)
**Status:** Proposed — addresses [issue #175](https://github.com/Kreyu/data-table-bundle/issues/175)
**Baseline pinned to:** commit `0ad7af1`, version `0.17`
**Depends on:** the pagination factory (#219), specified in
[query-and-data-sources.md](query-and-data-sources.md) §3a — **strictly required only for the
count-less view-model.** Counted mode reuses the existing `Pagination` unchanged (COUNT still runs, so page
numbers stay valid) and could in principle ship without #219; count-less mode needs the factory to
substitute a `KeysetPagination` view-model. Recommended sequencing: land #219 first so both modes route
construction through one seam.

Offset/limit pagination degrades on large datasets (the database scans and discards `OFFSET` rows).
Keyset (a.k.a. cursor / seek) pagination instead filters on the last-seen sort key
(`WHERE (sortcols) > (:cursor) LIMIT n`), which stays fast at any depth and is well-suited to large
exports. This document specifies **the chosen seam, the hard constraints, and a phased plan** — it
deliberately defers the full per-component test/error matrices until the approach is ratified (scope
agreed with maintainer).

## 1. Baseline (verified) — where offset pagination lives today

| Fact | Source |
|------|--------|
| Pagination is applied through one seam: `ProxyQueryInterface::paginate(PaginationData)` | `src/Query/ProxyQueryInterface.php:14` |
| Doctrine applies offset/limit: `setFirstResult($offset)->setMaxResults($perPage)` | `src/Bridge/Doctrine/Orm/Query/DoctrineOrmProxyQuery.php` `paginate()` (`:63-69`) |
| Array backend slices: `array_slice($data, $offset, $perPage)` | `src/Query/ArrayProxyQuery.php` `paginate()` (`:54-61`) |
| `PaginationData` carries only `page` + `perPage`; `getOffset() = perPage * (page - 1)` (unguarded — must not be called in keyset mode) | `src/Pagination/PaginationData.php:12-16,65-68` |
| `PaginationData` **is** the unit persisted (`setPersistenceData(PersistenceContext::Pagination, $data)`) and validated by a rigid `fromArray` whitelist (`page`/`perPage` only) | `src/DataTable.php:526`, `src/Pagination/PaginationData.php:21-43` |
| Total count comes from a Doctrine `Paginator` COUNT / array `count`, surfaced via `ResultSet` | [query-and-data-sources.md](query-and-data-sources.md) §2 |
| `sort()` resets pagination to page 1; `filter()` restores `originalQuery` then resets to page 1 | [core-data-table.md](core-data-table.md) §5 (`src/DataTable.php:535-597`) |
| The view-model `Pagination` is page-number oriented (`getPageCount`, `getFirst/LastVisiblePageNumber`) | `src/Pagination/Pagination.php:45-74` |
| URLs are built as `?{page_param}=N` | `src/Pagination/PaginationUrlGenerator.php:35` |

Offset arithmetic (`getOffset`) is the only place that assumes a numeric page maps to a row position.
Everything else (counts, sorting, the page-number view-model) is reusable under the "countable" keyset
model below.

## 2. Decided behavior model (confirmed with maintainer)

**Countable by default; pages become cursor-backed bookmarks; an opt-in flag enables a count-less mode.**

| Mode | COUNT issued? | Numbered pages? | Navigation | Use case |
|------|---------------|-----------------|-----------|----------|
| **Counted keyset** (default) | Yes | Yes — same windowed UI as offset | next / prev / jump, via per-boundary cursors ("bookmarks") | Drop-in faster replacement for offset, UI unchanged |
| **Count-less keyset** (`count_less: true`) | No | No | next / prev only | Maximum performance, infinite-scroll / "load more" feel, large exports |

**Bookmark model (counted mode).** Each page boundary is identified by an **opaque cursor** — the sort-key
tuple of that boundary row. As the user pages, the cursor for each visited boundary is remembered, so
next/prev and re-visiting passed pages issue a keyset `WHERE` instead of `OFFSET`. Because COUNT still
runs, `getPageCount()` and the windowed page-link UI keep working unchanged.

**The one honest leak (documented constraint, not a bug).** A keyset query alone cannot jump to an
*arbitrary, not-yet-visited* page (e.g. clicking "page 8" from page 1) — there is no cursor for that
boundary yet. Resolution options, to be chosen in Phase 1 (Open Question OQ-4):
- (a) fall back to `OFFSET` for that single jump, then bookmark the resulting boundary cursor; or
- (b) in count-less mode the problem is moot (no arbitrary page links are rendered).
Default lean: **(a)** — keyset for sequential paging (the common, hot path), offset fallback only for
arbitrary jumps. This preserves the existing UX while delivering the perf win where it matters.

## 3. Chosen seam

**Keep the minimal `paginate(PaginationData)` seam. Do NOT add a method to `ProxyQueryInterface`** — the
existing spec records "don't add methods to `ProxyQueryInterface`" as an anti-pattern
([query-and-data-sources.md](query-and-data-sources.md):86); the seam stays minimal.

Make `PaginationData` able to express a keyset request. Two candidate shapes (decide in Phase 1, OQ-1):

- **K-A — strategy + cursor on `PaginationData`:** add an enum `PaginationStrategy { Offset, Keyset }` and an
  optional opaque `cursor` payload. `DoctrineOrmProxyQuery::paginate` branches on the strategy.
- **K-B — `KeysetPaginationData` subtype:** a sibling carrying `cursor` + `direction` (after/before);
  `paginate()` does `instanceof`.

Lean: **K-A** keeps a single request type flowing through the existing `paginate`/persistence/event
plumbing with the least branching in `DataTable`; the strategy is a per-table option resolved at build.

**Cursor lifecycle (reconciles C-2/C-3 with C-4).** Because `PaginationData` is the persisted unit
(`src/DataTable.php:526`) and its `fromArray` whitelists only `page`/`perPage`
(`src/Pagination/PaginationData.php:21-43`), the live cursor must be **request-scoped, not persisted**.
Phase 1 must specify one of: (a) carry only the *strategy* on `PaginationData` (persisted, safe) and pass
the cursor as a separate transient parameter into the keyset branch — sidesteps C-4 by construction
(**lean**); or (b) if the cursor rides on `PaginationData`, mark it a transient property that the
persistence adapter and `fromArray` both ignore. Under either, "drop the cursor on sort/filter" (C-2/C-3)
means clearing that request-scoped value — there is nothing persisted to clear, so C-4 holds with no
contradiction.

The keyset **view-model** is a separate `PaginationInterface` implementation, produced by the
`PaginationFactoryInterface` from #219:
- counted mode → reuse `Pagination` (page numbers stay valid because COUNT runs);
- count-less mode → a new `KeysetPagination implements PaginationInterface` exposing `hasNextPage`/
  `hasPreviousPage` + next/prev cursors. `PaginationView` calls all 12 interface methods unconditionally
  (`src/Pagination/PaginationView.php:17-31`), so the page-number/index methods must return *something*;
  the **specific** neutral values are deferred to Phase 2 and decided **together with** the count-less
  theme template + a `count_less`/`has_total` view flag (C-5), so the returned values are chosen to make
  the template correct rather than picked abstractly now. The factory selects which view-model to build
  based on the resolved strategy + `count_less`.

Rejected: a new `ProxyQueryInterface` method (breaks the minimal-seam invariant); a fully separate
parallel pagination pipeline (duplicates persistence/events/request handling).

## 4. Hard constraints (properties of keyset — must be honored, not optional)

| # | Constraint | Implication |
|---|-----------|-------------|
| C-1 | Requires a **deterministic, unique total ordering** — the sort must end in a unique tiebreaker (typically the entity PK) | Keyset couples to `SortingData`. If the resolved sort is not total+unique, the implementation must append the PK (or refuse keyset). Specify the PK-append rule in Phase 1. |
| C-2 | Cursor validity depends on the **current sort**; changing the sort invalidates cursors | `sort()` already resets pagination — it must also drop the cursor (extend the existing reset). |
| C-3 | Cursor validity depends on the **current filters** | `filter()` already resets to page 1 + restores `originalQuery` — it must also drop the cursor. |
| C-4 | Cursors are **transient** (data-dependent) | They must NOT be persisted like `page`/`perPage`. Keyset persistence stores at most the *strategy choice*, never a live cursor. |
| C-5 | Count-less mode has **no total** | The page-number view-model is unavailable; the theme must render next/prev controls only. |
| C-6 | The cursor in a URL is **opaque** | Encode the boundary row's sort-key tuple, base64url, and validate on read (reject malformed → treat as no cursor / first page). Never expose raw column values as a contract. |

## 5. Backend feasibility

| Backend | Keyset feasibility |
|---------|--------------------|
| Doctrine ORM | Replace `setFirstResult/setMaxResults` with a `WHERE (col₁,…) > (:c₁,…) ORDER BY … LIMIT n` built from the resolved `SortingData`. Composite cursors need row-value comparison or its expanded boolean form. COUNT (counted mode) reuses the existing `Paginator` path. |
| PHP array | Data is already fully in memory; keyset offers no real benefit. Reuse offset slicing internally and present the same view-model. Acceptable to declare keyset a no-op-optimization here. |

## 6. Phased plan (each phase independently shippable)

| Phase | Scope | Depends on |
|-------|-------|-----------|
| **0** | This proposal — seam + constraints + behavior model agreed | — |
| **1** | `PaginationData` keyset capability (§3 K-A); `KeysetPagination` view-model; `PaginationFactory` selects view-model by strategy + `count_less`; Doctrine `paginate()` keyset branch (counted, single + composite ordering per OQ-2); option `pagination_strategy: offset\|keyset` + `pagination_count_less: bool`; sort/filter cursor-reset (C-2, C-3) | **#219** |
| **2** | Request handling: read `after`/`before` cursor params; `PaginationUrlGenerator` emits cursor URLs (C-6); keyset next/prev theme template (count-less) | Phase 1 |
| **3** | Export path — `DataTable::export` re-paginates "to all" (`src/DataTable.php:623-655`); keyset cursor-batching is the natural fit the issue calls out for large exports; array backend handling (§5) | Phase 1 |
| **4** | Persistence rules (C-4), user docs (`features/pagination.md`, `reference/configuration.md`), full test + error matrices | Phases 1–3 |

## 7. Open Questions (resolve before Phase 1 becomes an executable spec)

| ID | Question | Why it matters | Blocks |
|----|----------|----------------|--------|
| OQ-1 | `PaginationData` strategy+cursor (K-A) vs. `KeysetPaginationData` subtype (K-B)? | Shapes the request type and the amount of branching in `DataTable`/persistence | Phase 1 design |
| OQ-2 | Composite (multi-column) cursors from day 1, or single-ordered-key keyset first? | Multi-column keyset SQL (row-value comparison) is materially more complex | Phase 1 scope |
| OQ-3 | Opt-in granularity: per-table option only, or also per-request? | Affects config surface + request handler | Phase 1/2 |
| OQ-4 | Arbitrary-page-jump in counted mode: OFFSET fallback (lean) vs. forbid jumps | UX vs. purity; the §2 "honest leak" | Phase 1 |
| OQ-5 | May offset & keyset coexist in one table (offset UI + keyset export)? | Determines if strategy is table-level or operation-level | Phase 1/3 |

## Assumptions

| Assumption | If wrong, then… |
|------------|-----------------|
| The resolved `SortingData` is available to `paginate()`'s backend at the point keyset must build its `WHERE` (sort is applied before result fetch). Verified order: request handler runs `sort` before `paginate` (`src/Request/HttpFoundationRequestHandler.php:36-38`), and `DataTable::sort` calls `query->sort` (`:535-562`). | Keyset would need the sort threaded explicitly into the cursor request; Phase 1 design changes. |
| #219's `PaginationFactoryInterface` lands first and is the view-model seam. | Keyset would have to introduce its own substitution point, duplicating #219. |

## Anti-Patterns (DO NOT) — for the eventual implementation

| Don't | Do instead | Why |
|-------|-----------|-----|
| Add a `paginateByKeyset()` method to `ProxyQueryInterface` | Express keyset through `PaginationData` on the existing `paginate()` | Preserves the documented minimal-seam invariant ([query-and-data-sources.md](query-and-data-sources.md):86) |
| Persist a live cursor as if it were `page`/`perPage` | Persist at most the strategy choice (C-4) | Cursors are data-dependent and go stale across requests |
| Run keyset over a non-deterministic sort | Append the PK to guarantee a total, unique order, or refuse keyset (C-1) | Non-total ordering produces skipped/duplicated rows |
| Put raw column values in the cursor URL as a contract | Encode opaque, base64url, validated (C-6) | Leaks schema and breaks when columns change |
| Render the windowed page-number UI in count-less mode | Render next/prev controls only (C-5) | There is no total page count |

## Test Case Specifications

Deferred until Phase 1 is ratified (per agreed proposal scope). Phase 1's executable spec must add: keyset
`WHERE`/`ORDER BY`/`LIMIT` construction (single + composite), boundary cursor encode/decode round-trip,
malformed-cursor handling, sort/filter cursor-reset, counted vs. count-less view-model selection, and the
arbitrary-jump fallback (OQ-4) — each meeting the per-component floors in
[the methodology] before code generation.

## Error Handling Matrix

Deferred until Phase 1 (as above). Known conditions to specify then: malformed/forged cursor (→ treat as
first page / reject), keyset requested with no deterministic sort (→ append PK or raise a configuration
error per C-1), count-less mode asked for a total (→ unsupported-operation guard).

## References

| Topic | Location | Anchor |
|-------|----------|--------|
| View-model factory seam (prerequisite, #219) | [query-and-data-sources.md](query-and-data-sources.md) | §3a |
| Baseline offset pagination & the `paginate` seam | [query-and-data-sources.md](query-and-data-sources.md) | §1, §3 |
| Minimal-seam anti-pattern this respects | [query-and-data-sources.md](query-and-data-sources.md) | Anti-Patterns |
| Sort/filter pagination reset | [core-data-table.md](core-data-table.md) | §5 |
| Export "re-paginate to all" path | `src/DataTable.php` | `:623-655` |
| Doctrine offset application being branched | `src/Bridge/Doctrine/Orm/Query/DoctrineOrmProxyQuery.php` | `paginate()` `:63-69` |
