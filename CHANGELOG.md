# 0.14

- **[Feature]** Data table events (see more)
- **[Feature]** Column `priority` option to allow setting order of columns (see more)
- **[Feature]** Column `visible` option to allow setting visibility of columns (see more)
- **[Feature]** Column `personalizable` option to allow excluding the column from personalization (see more)
- **[Feature]** More verbose filter type form-related options such as `form_type`, `operator_form_type` (see more)
- **[Feature]** Ability to set hydration mode of the Doctrine ORM proxy query (see more)
- **[Feature]** Data table builder's `setSearchHandler` method for easier search definition (see more)
- **[Feature]** Collection column type default separator changed `', '` (with space after comma) instead of `','`
- **[Feature] [Breaking change]** The data table type persistence subject options are removed in favor of subject provider options (see more)
- **[Feature] [Breaking Change]** Optimized exporting process - introduces breaking changes (see more)
- **[Bugfix]** CollectionColumnType now renders without spaces around separator

Internally, the columns, filters and exporters are now utilizing the builder pattern similar to data tables.
Please note that this is a **breaking change** for applications using internal bundle classes!

For a list of all breaking changes, see the [upgrade guide](docs/upgrade-guide/0.14.md).

# 0.13

- **[Feature]** Batch actions ([see more](https://data-table-bundle.swroblewski.pl/features/actions/batch-actions/))
- **[Feature]** Improved DX with row actions ([see more](https://data-table-bundle.swroblewski.pl/features/actions/row-actions/))
- **[Feature]** Actions `visible` option ([see more](https://data-table-bundle.swroblewski.pl/reference/actions/types/action/#visible))
- **[Feature]** NumberColumnType with optional Intl integration ([see more](https://data-table-bundle.swroblewski.pl/reference/columns/types/number/))
- **[Feature]** MoneyColumnType with optional Intl integration ([see more](https://data-table-bundle.swroblewski.pl/reference/columns/types/money/))

Internally, the actions are now utilizing the builder pattern similar to data tables.
Please note that this is a **breaking change** for applications using internal bundle classes!