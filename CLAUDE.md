# CLAUDE.md

Guidance for working in this repository.

## What this is

`kreyu/data-table-bundle` — a Symfony bundle for building data tables. Its architecture is a
deliberate port of the Symfony Form component to the table domain: type classes resolved through a
registry, mutable **builder** → immutable **config** → runtime **instance** → **view**, decorated by
type extensions. Built-in type classes are `final`; share behavior through `getParent()` composition,
never PHP inheritance.

## Layout

| Path | What |
|------|------|
| `src/` | Bundle source (PSR-4 `Kreyu\Bundle\DataTableBundle\`) |
| `tests/` | PHPUnit tests (`tests/Unit/…`) and `tests/Fixtures/…` |
| `docs/specs/` | Reverse-engineered **architecture** specs (how it works internally, with file+line citations) |
| `docs/src/` | User-facing **VitePress** docs (how to *use* the bundle) — `docs/src/docs/features/*`, `docs/src/reference/*` |

## Required workflow for every change

1. **Run the tests** — they must stay green before you consider a change done:
   ```bash
   vendor/bin/phpunit
   ```
   Also run the quality gates the CI runs:
   ```bash
   vendor/bin/php-cs-fixer fix --dry-run --diff      # code style (must be clean)
   vendor/bin/phpstan analyse --memory-limit=1G       # static analysis
   ```
   Or use the Dockerised CI mirror: `make test`, `make quality`, or `make ci` (see `Makefile`).
   When you add or change behavior, add or update tests for it.

2. **Update the user docs** — whenever you add or change anything user-facing (a new option, config
   node, Twig theme block, controller helper, or behavior), update the VitePress docs under
   `docs/src/` in the **same** change:
   - the relevant feature page under `docs/src/docs/features/`;
   - the config reference `docs/src/reference/configuration.md` (both the YAML **and** PHP example
     blocks) when you touch the config tree or add an option;
   - the Twig reference `docs/src/reference/twig.md` when you touch theme blocks/functions.
   Match the existing page style: `::: code-group` tabs (Globally YAML / Globally PHP / For data table
   type / For specific data table) and `::: tip` / `::: warning` callouts. The architecture specs in
   `docs/specs/` are separate — update them only when asked; they are not a substitute for user docs.

## Conventions

- **Comments (Symfony style): default to none.** Allowed: one-sentence class-level PHPDoc, `@internal`,
  `@param`/`@return`/`@throws` only when the type hint is insufficient, `@deprecated` with a migration
  path, and inline `//` only for a non-obvious *why*. No WHAT-comments, no TODO/FIXME, no multi-line
  blocks.
- Don't break the public contract without cause: adding methods to `*ConfigInterface` /
  `*ConfigBuilderInterface` breaks external implementers — prefer a plain resolved option read via
  `getOption()` when the runtime only needs a value (see `page_visible_range`, `async`).
- New types are auto-registered via DI tags — implement the interface, never `new` or manually register
  them.
