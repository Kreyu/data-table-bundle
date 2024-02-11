# 0.17

## Breaking changes

### Type classes are now final

Built-in data table, column, filter, action and exporter types are now marked as "final".
This promotes the usage of `getParent()` method instead of PHP inheritance.

### Removed extension mechanisms

The extensions (not *type* extensions!) were a mechanisms copied from Symfony Form component.
Their main purpose was to provide a way to load type and type extension classes manually.
In most cases, that would be used only outside the framework, which is out of scope of this bundle.

Everything related with extensions is now completely removed.
Every type and type extension class defined in the container is now passed directly to the registry classes.

# 0.16

- **[Feature]** French translation (https://github.com/Kreyu/data-table-bundle/pull/53)
- **[Feature]** Doctrine ORM expression transformers with built-in `lower`, `upper` and `trim` options (https://github.com/Kreyu/data-table-bundle/issues/50)
- **[Feature]** Filter handler events
- **[Docs]** New documentation, written using [VitePress](https://vitepress.dev/)

# 0.15

- **[Feature]** Integration with AssetMapper (https://github.com/Kreyu/data-table-bundle/issues/42)

# 0.14

- **[Feature]** Data table events
- **[Feature]** Column `priority` option to allow setting order of columns
- **[Feature]** Column `visible` option to allow setting visibility of columns
- **[Feature]** Column `personalizable` option to allow excluding the column from personalization
- **[Feature]** More verbose filter type form-related options such as `form_type`, `operator_form_type`
- **[Feature]** Ability to set hydration mode of the Doctrine ORM proxy query
- **[Feature]** Data table builder's `setSearchHandler` method for easier search definition
- **[Feature]** The `CollectionColumnType` default separator changed `', '` (with space after comma) instead of `','`
- **[Feature]** Ability to create `ExportData` with exporter name string
- **[Feature]** Ability to provide property path in the `SortingColumnData`. The data table ensures valid property path is given (backwards compatible)
- **[Feature]** The Doctrine ORM `EntityFilterType` no longer requires `form_options.choice_value` option as the identifier field name will be retrieved from Doctrine class metadata by default
- **[Feature]** The `DateColumnType` that works exactly like `DateTimeColumnType`, but with date-only format by default
- **[Breaking change]** The data table type persistence subject options are removed in favor of subject provider options (see more)
- **[Breaking change]** Optimized exporting process - introduces breaking changes (see more)
- **[Breaking change]** The `DataTableBuilder` methods to add columns, filters, actions and exporters has changed definition - the `type` argument is now nullable to prepare for future implementation of type guessers
- **[Bugfix]** Fixed a bug in personalization form where changing the column visibility resulted in an exception
- **[Bugfix]** The `CollectionColumnType` now renders without spaces around separator
- **[Bugfix]** Default export data is now properly used within the export form 

Internally, the columns, filters and exporters are now utilizing the builder pattern similar to data tables.
Please note that this is a **breaking change** for applications using internal bundle classes!

For a list of all breaking changes and deprecations, see the [upgrade guide](../../../docs/upgrade-guide/0.14.md).

# 0.13

- **[Feature]** Batch actions ([see more](https://data-table-bundle.swroblewski.pl/features/actions/batch-actions/))
- **[Feature]** Improved DX with row actions ([see more](https://data-table-bundle.swroblewski.pl/features/actions/row-actions/))
- **[Feature]** Actions `visible` option ([see more](https://data-table-bundle.swroblewski.pl/reference/actions/types/action/#visible))
- **[Feature]** NumberColumnType with optional Intl integration ([see more](https://data-table-bundle.swroblewski.pl/reference/columns/types/number/))
- **[Feature]** MoneyColumnType with optional Intl integration ([see more](https://data-table-bundle.swroblewski.pl/reference/columns/types/money/))

Internally, the actions are now utilizing the builder pattern similar to data tables.
Please note that this is a **breaking change** for applications using internal bundle classes!
