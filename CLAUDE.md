# CLAUDE.md

`kreyu/data-table-bundle`: a Symfony bundle for data tables, modelled on the Symfony Form component.
Types resolve through a registry. Each table goes builder → config → `DataTable` → `DataTableView`, and type extensions decorate each step.

## Layout

| Path | What |
|------|------|
| `src/` | Bundle source, `Kreyu\Bundle\DataTableBundle\` |
| `src/{Column,Filter,Action,Exporter}/Type/` | Built-in types |
| `src/Bridge/` | Doctrine ORM, OpenSpout, PhpSpreadsheet integrations |
| `src/Resources/config/*.php` | Service definitions |
| `src/Resources/views/themes/` | Twig themes: `base` → `bootstrap_5` → `tabler`; `*icons*` themes define only the `icon` block |
| `src/Resources/translations/` | `KreyuDataTable.<locale>.yaml` |
| `tests/Unit/`, `tests/Integration/` | PHPUnit; shared fixtures in `tests/Fixtures/` |
| `docs/src/` | User docs (VitePress) |
| `docs/specs/` | Internal architecture notes |

## Checks

Default PHP is 8.1, the supported minimum; set `PHP_VERSION=8.5` to test the newest.

```bash
make install    # build image + composer update (rerun after editing composer.json)
make test       # PHPUnit
make quality    # Mago: fmt --check + lint + analyze
make fmt        # apply Mago formatting
make matrix     # PHPUnit on PHP 8.1–8.5 × lowest/highest deps
```

Mago baselines (`mago-*-baseline.toml`) match PHP 8.5 dependencies, so run `make quality` after `make install PHP_VERSION=8.5`.

A change is done when `make test` and `make quality` pass.

## Rules

- **Share behavior through `getParent()`**, never PHP inheritance. Built-in types are `final`.
- **Other classes: interface or `final`.** Anything replaceable (factories, generators, adapters) gets an interface and stays open; classes without one (value objects, events) are `final`.
- **New types and extensions register themselves.** Implement the interface; DI autoconfiguration adds the tag.
- **Expose features through interfaces.** New config and behavior get methods on the matching interface (e.g. `DataTableConfigInterface::getPaginationFactory()`). That breaks third-party implementers, so name the added methods in the commit message.
- **Shared markup goes in `base.html.twig`.** `bootstrap_5` and `tabler` override single blocks only.
- **Keep the user docs current in the same change.** Anything users touch counts: options, config keys, public methods, theme blocks, behavior. Update:
  - feature page in `docs/src/docs/features/`
  - `docs/src/reference/configuration.md` (YAML and PHP examples) for config
  - `docs/src/reference/twig.md` for theme blocks or Twig functions
  - match the existing `::: code-group` tabs and `::: tip` / `::: warning` callouts
- **Leave `docs/specs/` alone** unless asked.
